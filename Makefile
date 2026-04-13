IMAGE := dto-forge

UID = $(shell id -u)
GID = $(shell id -g)
export UID
export GID

# Output colors
GREEN = \033[0;32m
YELLOW = \033[0;33m
RED = \033[0;31m
NC = \033[0m # No Color

build:
	docker build \
	  --build-arg UID=$(UID) \
	  --build-arg GID=$(GID) \
	  -t $(IMAGE) .

install:
	docker run --rm -it \
	  -v $(PWD):/app \
	  $(IMAGE) composer install

update:
	docker run --rm -it \
	  -v $(PWD):/app \
	  $(IMAGE) composer update

# Known test groups
KNOWN_GROUPS=
test-groups:
	@echo "$(GREEN)Available test groups:$(NC)"
	@for group in $(KNOWN_GROUPS); do echo "  - $$group"; done

test:
	@echo "$(GREEN)Running all tests...$(NC)"
	docker run --rm -it -v $(PWD):/app $(IMAGE) ./vendor/bin/pest

test-%: ## Run tests for the specified group
	@if echo "$(KNOWN_GROUPS)" | grep -wq "$*"; then \
		echo "$(GREEN)Running tests for group '$*'...$(NC)"; \
		docker run --rm -it -v $(PWD):/app $(IMAGE) ./vendor/bin/pest --group=$*; \
	else \
		echo "$(RED)Unknown group '$*'.$(NC)"; \
		$(MAKE) test-groups; \
		exit 1; \
	fi

shell:
	docker run --rm -it -v $(PWD):/app $(IMAGE) bash

pint:
	docker run --rm -it -v $(PWD):/app $(IMAGE) ./vendor/bin/pint

stan:
	docker run --rm -it -v $(PWD):/app $(IMAGE) ./vendor/bin/phpstan analyse --memory-limit=256M