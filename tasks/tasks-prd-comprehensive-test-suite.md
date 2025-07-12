# Task List: Comprehensive Test Suite Implementation

Based on the PRD for implementing a comprehensive test suite for the Symfony Message Tracing Bundle.

## Relevant Files

- `composer.json` - Add testing dependencies (symfony-test libraries)
- `phpunit.dist.xml` - Update test directory configuration
- `tests/AbstractTestCase.php` - Base test utilities and common patterns
- `tests/Fixtures/TestKernel.php` - Custom kernel for multi-version testing
- `tests/Fixtures/MockTraceStack.php` - Mock implementation for testing
- `tests/Fixtures/MockTraceGenerator.php` - Mock trace generator for isolation
- `tests/Unit/Bundle/SimensenSymfonyMessageTracingBundleTest.php` - Bundle class tests
- `tests/Unit/Middleware/CausationMiddlewareTest.php` - Unit tests for causation middleware
- `tests/Unit/Middleware/CorrelationMiddlewareTest.php` - Unit tests for correlation middleware
- `tests/Integration/BundleConfigurationTest.php` - Configuration and DI container tests
- `tests/Integration/ServiceRegistrationTest.php` - Service registration validation tests
- `tests/Functional/MiddlewareIntegrationTest.php` - End-to-end middleware testing
- `tests/Functional/MessengerIntegrationTest.php` - Full Symfony Messenger integration
- `.github/workflows/tests.yml` - CI/CD pipeline with version matrix

### Notes

- Tests follow PSR-4 namespace `Simensen\SymfonyMessageTracingBundle\Tests\`
- Use `make tests` or `make phpunit` to run the test suite locally
- GitHub Actions will automatically test across Symfony versions 6.4, 7.0, 7.1, 7.2
- PHPStan level 9 must pass with zero errors before merging

## Tasks

- [ ] 1.0 Set Up Test Infrastructure and Dependencies
  - [ ] 1.1 Install symfony-test/symfony-dependency-injection-test dependency
  - [ ] 1.2 Install symfony-test/symfony-bundle-test dependency
  - [ ] 1.3 Create tests/ directory structure with Unit/, Integration/, Functional/, Fixtures/ subdirectories
  - [ ] 1.4 Update phpunit.dist.xml to point to tests/ directory instead of example/tests
  - [ ] 1.5 Configure PSR-4 autoloading for test namespace in composer.json
  - [ ] 1.6 Create AbstractTestCase.php with common test utilities and helper methods
  - [ ] 1.7 Create TestKernel.php fixture for multi-version Symfony compatibility testing

- [ ] 2.0 Implement Bundle Configuration and Service Registration Tests  
  - [ ] 2.1 Create BundleConfigurationTest extending AbstractExtensionTestCase
  - [ ] 2.2 Test default configuration loading with no user-provided config
  - [ ] 2.3 Test custom trace_generator configuration with valid class names
  - [ ] 2.4 Test custom trace_stack configuration with valid class names
  - [ ] 2.5 Test trace_identity configuration with different types and generators
  - [ ] 2.6 Test messenger middleware configuration options
  - [ ] 2.7 Test invalid configuration scenarios and proper error handling
  - [ ] 2.8 Test configuration edge cases (null values, empty arrays, invalid types)
  - [ ] 2.9 Create ServiceRegistrationTest to validate all services are properly registered
  - [ ] 2.10 Test service definitions have correct class names and dependencies
  - [ ] 2.11 Test autowiring and autoconfiguration settings are applied correctly
  - [ ] 2.12 Test service tags (messenger.middleware) are applied with correct priorities

- [ ] 3.0 Create Middleware Unit and Functional Tests
  - [ ] 3.1 Create MockTraceStack and MockTraceGenerator fixtures for isolation
  - [ ] 3.2 Create CausationMiddlewareTest with unit tests for message wrapping
  - [ ] 3.3 Test CausationMiddleware trace context pushing and popping behavior
  - [ ] 3.4 Test CausationMiddleware error handling and exception scenarios
  - [ ] 3.5 Create CorrelationMiddlewareTest with unit tests for message wrapping
  - [ ] 3.6 Test CorrelationMiddleware trace context pushing and popping behavior
  - [ ] 3.7 Test CorrelationMiddleware error handling and exception scenarios
  - [ ] 3.8 Create MiddlewareIntegrationTest for functional testing with real message bus
  - [ ] 3.9 Test middleware interaction with external tracing packages (mocked)
  - [ ] 3.10 Test middleware behavior with various message types and envelope configurations

- [ ] 4.0 Develop Integration and End-to-End Tests
  - [ ] 4.1 Create MessengerIntegrationTest extending KernelTestCase
  - [ ] 4.2 Test complete bundle loading in a real Symfony kernel environment
  - [ ] 4.3 Test bundle integration with Symfony Messenger component
  - [ ] 4.4 Test integration with simensen/message-tracing packages (mocked interfaces)
  - [ ] 4.5 Test end-to-end message tracing through complete middleware stack
  - [ ] 4.6 Test bundle behavior when loaded alongside other common Symfony bundles
  - [ ] 4.7 Create SimensenSymfonyMessageTracingBundleTest for bundle class testing
  - [ ] 4.8 Test bundle's configure() method and configuration schema validation
  - [ ] 4.9 Test bundle's loadExtension() method and service registration process

- [ ] 5.0 Configure CI/CD Pipeline with Multi-Version Testing
  - [ ] 5.1 Create .github/workflows/tests.yml GitHub Actions workflow file
  - [ ] 5.2 Configure test matrix for PHP versions and Symfony versions (6.4, 7.0, 7.1, 7.2)
  - [ ] 5.3 Set up automated PHPUnit test execution in CI environment
  - [ ] 5.4 Integrate PHPStan analysis with zero error requirement
  - [ ] 5.5 Configure workflow triggers for pull requests and main branch pushes
  - [ ] 5.6 Set up test reporting and failure notification
  - [ ] 5.7 Add Makefile integration for CI (make tests, make phpstan commands)
  - [ ] 5.8 Test deprecation warnings and forward compatibility across Symfony versions