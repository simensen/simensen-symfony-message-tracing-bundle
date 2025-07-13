# Symfony Message Tracing Bundle

A Symfony bundle that provides causation and correlation tracing for Symfony Messenger buses. This bundle integrates with the `simensen/message-tracing` and `simensen/symfony-messenger-message-tracing` packages to enable distributed tracing of message flows.

## Installation

```bash
composer require simensen/symfony-message-tracing-bundle
```

## Features

- **Causation Tracing**: Track which messages caused other messages to be dispatched
- **Correlation Tracing**: Group related messages together across service boundaries
- **Flexible Identity Generation**: Support for UUID, ULID, or custom trace identity generators
- **Easy Configuration**: Simple configuration options with sensible defaults
- **Symfony Integration**: Seamless integration with Symfony Messenger middleware system

## Configuration

### Basic Configuration

The bundle works out of the box with default settings. Add this to your configuration if you want to customize it:

```yaml
# config/packages/simensen_symfony_message_tracing.yaml
simensen_symfony_message_tracing:
    trace_identity_generator: uuid  # Default: 'uuid' (auto-selects appropriate generator)
    # trace_generator is auto-configured based on trace_identity_generator
    # trace_stack: Simensen\MessageTracing\TraceStack\Adapter\DefaultTraceStack  # Optional override
```

### Trace Identity Generators

Choose from built-in generators or provide your own:

```yaml
simensen_symfony_message_tracing:
    # Built-in options (auto-configures appropriate trace generator)
    trace_identity_generator: uuid  # Uses UuidTraceIdentityGenerator & UuidMessageTracingStampGenerator (default)
    # trace_identity_generator: ulid  # Uses UlidTraceIdentityGenerator & UlidMessageTracingStampGenerator
    
    # Custom implementation (requires manual trace_generator configuration)
    # trace_identity_generator: App\Tracing\CustomTraceIdentityGenerator
    # trace_generator: App\Tracing\CustomMessageTracingStampGenerator
```

### Messenger Middleware Configuration

Configure the tracing middleware for your message buses:

```yaml
# config/packages/messenger.yaml
framework:
    messenger:
        buses:
            command.bus:
                middleware:
                    - simensen_message_tracing.middleware.causation
                    
            event.bus:
                middleware:
                    - simensen_message_tracing.middleware.correlation
                    
            query.bus:
                middleware:
                    - simensen_message_tracing.middleware.causation
                    - simensen_message_tracing.middleware.correlation
```

## Complete Example

Here's a complete configuration example that enables ULID trace identities and adds specific middleware to different buses:

```yaml
# config/packages/simensen_symfony_message_tracing.yaml
simensen_symfony_message_tracing:
    trace_identity_generator: ulid

# config/packages/messenger.yaml
framework:
    messenger:
        buses:
            command.bus:
                middleware:
                    - simensen_message_tracing.middleware.causation
                    
            event.bus:
                middleware:
                    - simensen_message_tracing.middleware.correlation
```

## Usage

Once configured, the bundle automatically traces message flows:

```php
// When you dispatch a message, it gets traced automatically
$this->commandBus->dispatch(new CreateUserCommand($userId, $email));

// Related messages will maintain causation/correlation relationships
$this->eventBus->dispatch(new UserCreatedEvent($userId));
```

## Middleware Services

The bundle registers these middleware services automatically:

- `simensen_message_tracing.middleware.causation` - Tracks causation relationships
- `simensen_message_tracing.middleware.correlation` - Tracks correlation relationships

## Requirements

- PHP 8.2 or higher
- Symfony 6.4 or 7.0+
- Symfony Messenger component

## Dependencies

This bundle builds on top of:

- [`simensen/message-tracing`](https://github.com/simensen/simensen-message-tracing) - Core tracing functionality
- [`simensen/symfony-messenger-message-tracing`](https://github.com/simensen/simensen-symfony-messenger-message-tracing) - Symfony Messenger integration

## License

This bundle is released under the MIT License. See the [LICENSE](LICENSE) file for details.

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.