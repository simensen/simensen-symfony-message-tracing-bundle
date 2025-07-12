# PRD: Comprehensive Test Suite for Symfony Message Tracing Bundle

## Introduction/Overview

This PRD outlines the implementation of a comprehensive test suite for the Simensen Symfony Message Tracing Bundle. The bundle currently has **zero test coverage** and requires a complete testing infrastructure to ensure reliability, maintainability, and compatibility across multiple Symfony versions. The test suite will validate bundle configuration, service registration, middleware functionality, and integration with external tracing packages.

**Problem**: The bundle lacks any automated testing, making it difficult to verify functionality, catch regressions, or ensure compatibility across supported Symfony versions (6.4, 7.0, 7.1, 7.2).

**Goal**: Establish a robust, comprehensive test suite that provides confidence in the bundle's functionality and enables safe refactoring and feature development.

## Goals

1. **Achieve comprehensive test coverage** for all bundle components (configuration, services, middleware)
2. **Validate compatibility** across Symfony versions 6.4, 7.0, 7.1, and 7.2
3. **Establish testing infrastructure** using industry-standard testing libraries
4. **Integrate with CI/CD pipeline** to ensure automated quality gates
5. **Enable TDD/BDD development practices** for future feature development
6. **Maintain zero PHPStan errors** at the current level (level 9)

## User Stories

**As a bundle maintainer**, I want comprehensive test coverage so that I can confidently release updates without breaking existing functionality.

**As a bundle maintainer**, I want automated testing across multiple Symfony versions so that I can ensure compatibility without manual verification.

**As a bundle contributor**, I want clear testing patterns and utilities so that I can write tests for new features following established conventions.

**As a bundle user**, I want confidence that the bundle works correctly in my Symfony application version so that I can integrate it without fear of runtime errors.

**As a CI/CD system**, I want automated test execution so that I can block deployments that break functionality or introduce regressions.

## Functional Requirements

### 1. Test Infrastructure Setup
1.1. Create `/tests` directory structure aligned with PSR-4 namespace `Simensen\SymfonyMessageTracingBundle\Tests\`
1.2. Install and configure SymfonyTest/SymfonyDependencyInjectionTest library
1.3. Install and configure SymfonyTest/symfony-bundle-test library  
1.4. Update PHPUnit configuration to point to correct test directories
1.5. Create base test classes and utilities for common testing patterns

### 2. Bundle Configuration Testing
2.1. Test default configuration loading with no user configuration
2.2. Test custom trace_generator configuration with valid class names
2.3. Test custom trace_stack configuration with valid class names
2.4. Test trace_identity configuration with different types and generators
2.5. Test messenger middleware configuration options
2.6. Test invalid configuration scenarios and error handling
2.7. Test configuration edge cases (null values, empty arrays, invalid types)

### 3. Service Registration Testing  
3.1. Test all services are properly registered in the container
3.2. Test service definitions have correct class names and dependencies
3.3. Test autowiring and autoconfiguration settings
3.4. Test service tags (messenger.middleware) are applied correctly
3.5. Test service priorities are set correctly (middleware priority 100)
3.6. Test conditional service registration based on configuration

### 4. Middleware Functional Testing
4.1. Test CausationMiddleware properly wraps message handling
4.2. Test CorrelationMiddleware properly wraps message handling  
4.3. Test middleware integration with Symfony Messenger bus
4.4. Test trace context pushing and popping behavior
4.5. Test middleware interaction with external tracing packages
4.6. Test middleware error handling and exception scenarios
4.7. Test middleware behavior with various message types

### 5. Integration Testing
5.1. Test complete bundle loading in a real Symfony kernel
5.2. Test bundle integration with Symfony Messenger component
5.3. Test integration with simensen/message-tracing packages
5.4. Test end-to-end message tracing through middleware stack
5.5. Test bundle behavior with other Symfony bundles installed

### 6. Multi-Version Compatibility Testing
6.1. Test bundle functionality on Symfony 6.4
6.2. Test bundle functionality on Symfony 7.0  
6.3. Test bundle functionality on Symfony 7.1
6.4. Test bundle functionality on Symfony 7.2
6.5. Test deprecation warnings and forward compatibility

### 7. GitHub Actions CI/CD Integration
7.1. Create workflow for automated test execution
7.2. Configure test matrix for multiple PHP and Symfony versions
7.3. Integrate PHPStan analysis with zero error requirement
7.4. Set up test reporting and coverage tracking
7.5. Configure workflow triggers for PR and main branch pushes

## Non-Goals (Out of Scope)

- **Performance benchmarking tests** - Focus on functional correctness, not performance
- **Load testing or stress testing** - Bundle is not performance-critical
- **Browser/E2E testing** - Bundle has no frontend components  
- **Database integration testing** - Bundle does not interact with databases
- **Multiple configuration formats** - Only YAML configuration testing (not XML/PHP)
- **Backward compatibility testing** - Only test supported Symfony versions
- **Documentation testing** - Code functionality only, not documentation accuracy

## Design Considerations

### Test Directory Structure
```
tests/
├── Functional/           # End-to-end bundle testing
├── Integration/          # Service integration tests  
├── Unit/                 # Unit tests for individual classes
│   ├── Middleware/       # Middleware unit tests
│   └── Bundle/           # Bundle class tests
├── Fixtures/             # Test fixtures and mock classes
└── AbstractTestCase.php  # Base test utilities
```

### Testing Patterns
- **AbstractExtensionTestCase** for configuration and DI testing
- **KernelTestCase** for integration testing with real Symfony kernel
- **Custom TestKernel** for multi-version compatibility testing
- **Mock objects** for external dependencies (tracing packages)

### Test Data Management
- **Fixtures** for test configuration scenarios
- **Mock classes** for simulating external package interfaces
- **Test kernels** for different Symfony version scenarios

## Technical Considerations

### Dependencies
- Add `symfony-test/symfony-dependency-injection-test: ^6.0|^7.0` to dev dependencies
- Add `symfony-test/symfony-bundle-test: ^3.0` to dev dependencies
- Ensure compatibility with existing PHPUnit 11.2+ requirement

### Configuration Updates
- Update `phpunit.dist.xml` to use `/tests` directory instead of `/example/tests`
- Configure test autoloading in `composer.json`
- Add GitHub Actions workflow file

### Mocking Strategy
- Mock external tracing package interfaces to isolate bundle testing
- Use Symfony's test containers for service testing
- Create test doubles for Messenger bus components

## Success Metrics

### Primary Success Criteria
1. **All tests pass** across the supported Symfony version matrix (6.4, 7.0, 7.1, 7.2)
2. **Zero PHPStan errors** maintained at level 9
3. **Complete test infrastructure** established with clear patterns for future development

### Quality Gates
- All CI/CD pipeline checks must pass before merging
- PHPStan analysis passes with zero errors
- All test suites (unit, integration, functional) execute successfully

### Measurable Outcomes
- Test execution time under reasonable limits (< 5 minutes total)
- Clear test failure reporting with actionable error messages
- Successful test execution in GitHub Actions environment

## Open Questions

1. **Should we include mutation testing** (e.g., Infection) to validate test quality?
2. **What level of test coverage reporting** should be implemented (if any)?
3. **Should integration tests use real message buses** or mock implementations?
4. **How should we handle testing of dev-branch dependencies** (`simensen/message-tracing: ^0.1@dev`)?
5. **Should we test bundle installation scenarios** (e.g., via Composer in clean projects)?
6. **What error reporting and logging strategies** should be tested?
7. **Should we include security testing** for potential vulnerabilities in tracing data?