# GitHub Actions Workflows

This directory contains GitHub Actions workflows for the Tournament App.

## Available Workflows

### 1. `test.yml` - Main Test Workflow (Recommended)
- **Trigger**: Pushes and Pull Requests to `feature/serbian-app` branch
- **Environment**: Ubuntu with PHP 8.1 and MySQL 8.0 service
- **Features**:
  - Runs all PHPUnit tests
  - Builds frontend assets with Vite
  - Uses parallel testing for faster execution
  - Uploads test artifacts on failure

### 2. `test-with-docker.yml` - Docker-based Testing (Alternative)
- **Trigger**: Manual dispatch only (disabled by default)
- **Environment**: Uses your existing Docker Compose setup
- **Features**:
  - Tests in an environment closer to production
  - Uses the same Docker containers as development
  - More complex setup but higher fidelity

## Configuration

### Environment Variables
The workflows use the `.env.testing` file for configuration. Key settings:
- Database: MySQL 8.0 with `tournament_app_testing` database
- Cache/Session: Array drivers for testing
- Mail: Array driver (no actual emails sent)

### Database Setup
Tests run against a fresh MySQL database that's created for each workflow run:
- Database: `tournament_app_testing`
- Host: `127.0.0.1` (for main workflow) or `db` (for Docker workflow)
- Credentials: `root/root`

### Asset Building
Frontend assets are built using Vite during the workflow to ensure JavaScript/CSS dependencies are properly compiled.

## Enabling/Disabling Workflows

### Main Workflow (test.yml)
This runs automatically on every push/PR to `feature/serbian-app`. To disable:
1. Change the `on:` section to `workflow_dispatch:`
2. Or delete the file entirely

### Docker Workflow (test-with-docker.yml)
This is disabled by default. To enable:
1. Change `workflow_dispatch:` to:
   ```yaml
   push:
     branches: [ feature/serbian-app ]
   pull_request:
     branches: [ feature/serbian-app ]
   ```

## Troubleshooting

### Test Failures
- Check the "Run tests" step output for specific test failures
- Test artifacts (logs) are uploaded automatically on failure
- Ensure your local tests pass before pushing

### Database Issues
- The workflow creates a fresh database for each run
- Check that migrations run successfully
- Verify `.env.testing` has correct database settings

### Asset Build Issues
- Ensure `package.json` has the correct `build` script
- Check that all npm dependencies are properly listed
- Verify Vite configuration is correct