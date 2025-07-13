# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Symfony bundle that provides causation and correlation tracing for Symfony Messenger buses. The bundle integrates with the `simensen/message-tracing` and `simensen/symfony-messenger-message-tracing` packages to enable distributed tracing of message flows.

## Architecture

The bundle follows the standard Symfony bundle architecture:

- **Main Bundle Class**: `SimensenSymfonyMessageTracingBundle` - Extends `AbstractBundle` and configures service definitions
- **Configuration**: The bundle provides configuration for trace generators, trace stacks, trace identity generators, and messenger middleware
- **Service Integration**: Uses dependency injection to register tracing services and middleware for Symfony Messenger

Key components:
- **Trace Stack**: Manages the current trace context (`DefaultTraceStack` by default)
- **Trace Generator**: Generates trace information (`MessageTracingStampGenerator` by default)
- **Trace Identity Generator**: Generates unique trace identities (uses Symfony UUIDs by default)
- **Messenger Middleware**: Provides causation and correlation tracing middleware for Symfony Messenger

## Development Commands

The project uses a Makefile for common development tasks:

### Setup
```bash
make tools        # Install development tools via PHIVE
composer install  # Install dependencies
```

### Code Quality
```bash
make cs           # Fix code style issues with php-cs-fixer
```

### Testing
```bash
make tests        # Run PHPUnit tests
make phpunit      # Run PHPUnit tests directly
make clover       # Generate test coverage report
```

### All-in-one
```bash
make it           # Run tools, install dependencies, fix code style, and run tests
```

### Dependency Analysis
```bash
make dependency-analysis  # Check for unused dependencies
```

### Cleanup
```bash
make clean        # Remove vendor and tools directories
make realclean    # Remove vendor, tools, and composer.lock
```

## Testing

- Tests are expected to be in the `example/tests` directory (as configured in `phpunit.dist.xml`)
- The bundle uses PHPUnit 11.2+ with strict configuration
- Coverage can be generated using `make clover` with Xdebug

## Code Standards

- PHP 8.2+ required
- Uses strict types (`declare(strict_types=1);`)
- Code style enforced via php-cs-fixer
- Dependencies managed through Composer with normalization

## Dependencies

The bundle depends on:
- Core Symfony components (Config, DependencyInjection, HttpKernel, Messenger)
- Custom tracing packages (`simensen/message-tracing`, `simensen/symfony-messenger-message-tracing`)
- Symfony UID component for trace identity generation

Development packages are installed via PHIVE (php-cs-fixer, PHPUnit, composer-require-checker).