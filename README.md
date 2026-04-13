# gRPC Resource Mapper

When you implement a gRPC service in PHP, `protoc` generates stub classes for your messages — `UserMessage`, `AddressMessage`, etc. Filling them manually is repetitive and error-prone:

```php
// Without this library — manual, every time
$message = new UserMessage();
$message->setId($user->id);
$message->setName($user->full_name);

$address = new AddressMessage();
$address->setCity($user->address->city);
$message->setAddress($address);

return $message;
```

This library solves that problem the Laravel way — the same pattern as `JsonResource` for REST APIs, but for gRPC and Protobuf. You define a resource class once, and it handles mapping, nesting, collections, and Field Masks automatically:

```php
#[ProtoMessage(UserMessage::class)]
class UserResource extends Resource
{
    public static function fields(): array
    {
        return [
            new Value('id'),
            new Value('name', 'full_name'),
            new Relation('address', 'address', AddressResource::class),
        ];
    }
}

// In your gRPC handler
return (new UserResource($user, $request->getFieldMask()))->toGrpc();
```

## Features

- **Declarative resources** — define structure once per resource class
- **Field Masks** — clients request only needed fields
- **Nested resources** — compose resources for nested objects
- **Collections** — repeated field support
- **Map fields** — protobuf `map<key, value>` support
- **OneOf** — Protobuf union type support

## Requirements

`google/protobuf` is not listed as an explicit dependency — any project using this library necessarily has generated proto stub classes, which already bring `google/protobuf` in as a transitive dependency.

## Installation
```bash
composer require gpalyan/proto-resource
```

## Quick Start

### 1. Create a Resource
```php
use ProtoResource\Attributes\ProtoMessage;
use ProtoResource\Resources\Resource;
use ProtoResource\Types\Value;

#[ProtoMessage(UserMessage::class)]
class UserResource extends Resource
{
    public static function fields(): array
    {
        return [
            new Value('id'),
            new Value('name', 'full_name'),
        ];
    }
}
```

### 2. Use the Resource
```php
// Without Field Mask (all fields)
$grpcMessage = (new UserResource($user))->toGrpc();

// With Field Mask (specific fields only)
$fieldMask = new \Google\Protobuf\FieldMask();
$fieldMask->setPaths(['id', 'name', 'address.city']);

$grpcMessage = (new UserResource($user, $fieldMask))->toGrpc();
```

### 3. Collections
```php
$collection = UserResource::collection($users, $fieldMask);

foreach ($collection as $grpcMessage) {
    // Each $grpcMessage is a ready gRPC message
}
```

## Field Types

### Value
Maps a single scalar field.
```php
// Same name in source and message
new Value('id'),

// Custom source key
new Value('name', 'full_name'),

// Computed value via callback
new Value('status', fn(User $user) => $user->is_active ? 'active' : 'inactive'),
```

### Relation
Maps a nested object to a proto message. Pass a Resource class to define the nested structure.
```php
use ProtoResource\Types\Relation;

// Using a nested resource (recommended)
new Relation('address', 'address', AddressResource::class),

// With explicit proto class override
new Relation('address', 'address', AddressResource::class, AddressMessage::class),

// Without a resource — uses autoFill
new Relation('address', fn($u) => [...], messageClass: AddressMessage::class),
```

### Repeated
Maps a collection of items to a repeated proto field.
```php
use ProtoResource\Types\Repeated;

// Using a nested resource (recommended)
new Repeated('posts', 'posts', PostResource::class),

// Without a resource — uses autoFill
new Repeated('posts', fn($u) => $u->posts->toArray(), messageClass: PostMessage::class),
```

### Map
Maps an associative array to a protobuf `map<key, value>` field.
```php
use ProtoResource\Types\Map;

// map<string, string> — scalar values
new Map('metadata'),

// With custom source key
new Map('metadata', 'meta'),

// map<string, Message> — using a nested resource
new Map('items', 'items', ItemResource::class),

// map<string, Message> — with explicit proto class
new Map('items', 'items', ItemResource::class, ItemMessage::class),
```

### OneOf
Resolves one field from a group based on a callable resolver.
```php
use ProtoResource\Types\OneOf;

new OneOf(
    name: 'result',
    fields: [
        'success' => new Relation('success', fn($r) => $r->data, SuccessResource::class),
        'error'   => new Relation('error',   fn($r) => $r->error, ErrorResource::class),
    ],
    resolver: fn($r) => match($r->status) {
        'ok'   => 'success',
        'fail' => 'error',
        default => null,
    }
),
```

## Nested Resources

Each resource encapsulates its own field definitions. Compose them via `Relation` or `Repeated`:

```php
#[ProtoMessage(AddressMessage::class)]
class AddressResource extends Resource
{
    public static function fields(): array
    {
        return [
            new Value('city'),
            new Value('street'),
            new Value('zipCode', 'zip_code'),
        ];
    }
}

#[ProtoMessage(PostMessage::class)]
class PostResource extends Resource
{
    public static function fields(): array
    {
        return [
            new Value('id'),
            new Value('title'),
            new Value('publishedAt', fn(Post $post) => $post->published_at?->timestamp),
            new Relation('author', 'user', UserResource::class),
        ];
    }
}

#[ProtoMessage(UserMessage::class)]
class UserResource extends Resource
{
    public static function fields(): array
    {
        return [
            new Value('id'),
            new Value('name', 'full_name'),
            new Relation('address', 'address', AddressResource::class),
            new Repeated('posts', 'posts', PostResource::class),
            new Map('metadata'),
        ];
    }
}
```

## License

MIT
