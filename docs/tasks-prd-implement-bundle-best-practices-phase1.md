# Tasks: Phase 1 - Critical Middleware Implementation

Based on the PRD requirements for Phase 1, here are the high-level tasks:

## Relevant Files

- `src/Middleware/CausationMiddleware.php` - Middleware class that implements MiddlewareInterface for causation tracing
- `src/Middleware/CorrelationMiddleware.php` - Middleware class that implements MiddlewareInterface for correlation tracing
- `src/SimensenSymfonyMessageTracingBundle.php` - Bundle class that needs service registration updates

### Notes

- Middleware classes should implement Symfony's MiddlewareInterface
- They should work with existing envelope managers (CausationTracedEnvelopeManager and CorrelationTracedEnvelopeManager)
- Services must be tagged with 'messenger.middleware' for auto-discovery

## Tasks

- [x] 1.0 Create Middleware Classes
  - [x] 1.1 Create src/Middleware directory
  - [x] 1.2 Implement CausationMiddleware class with MiddlewareInterface
  - [x] 1.3 Implement CorrelationMiddleware class with MiddlewareInterface
  - [x] 1.4 Add proper constructor dependency injection for envelope managers
- [x] 2.0 Fix Bundle Service Registration
  - [x] 2.1 Register CausationMiddleware as a service
  - [x] 2.2 Register CorrelationMiddleware as a service  
  - [x] 2.3 Configure services to use existing envelope managers
  - [x] 2.4 Clean up commented code from bundle class
- [x] 3.0 Implement Automatic Middleware Registration
  - [x] 3.1 Add messenger.middleware tags to both middleware services
  - [x] 3.2 Configure middleware priority/ordering for proper execution
  - [x] 3.3 Verify middleware services are discoverable by Symfony Messenger
- [ ] 4.0 Ensure Proper Service Aliasing
- [ ] 5.0 Validate Integration with Symfony Messenger