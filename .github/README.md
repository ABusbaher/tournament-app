# GitHub Actions Workflows

This directory contains GitHub Actions workflows for the Tournament App.

## Available Workflows

### 1. `test.yml` - CI Pipeline (Main) ✅
- **Trigger**: Currently disabled (commented out)
- **Environment**: Ubuntu with PHP 8.1 and MySQL 8.0 service
- **Configuration**: Creates `.env` file directly in workflow
- **Pipeline Steps**:
  - Installs PHP and Node.js dependencies
  - Builds frontend assets with Vite
  - Configures testing environment
  - Runs database migrations
  - Executes PHPUnit test suite
  - Uploads failure artifacts
- **Features**: Fast, reliable, no external dependencies required

### 2. `test-with-docker.yml` - CI Pipeline with Docker
- **Trigger**: Manual dispatch only (disabled by default)
- **Environment**: Uses your existing Docker Compose setup
- **Configuration**: Creates `.env` file for Docker environment
- **Pipeline Steps**: Same as main pipeline but runs inside Docker containers
- **Features**: Higher environment fidelity, matches development setup exactly

### 3. `test-with-secrets.yml` - CI/CD Pipeline with Secrets ✅
- **Trigger**: Pushes and Pull Requests to `feature/serbian-app` branch
- **Environment**: Ubuntu with configurable GitHub secrets
- **Configuration**: Uses repository secrets for sensitive configuration
- **Pipeline Steps**:
  - Environment configuration with secrets
  - Complete dependency installation
  - Frontend asset building
  - Database setup and migrations
  - Comprehensive test execution
  - Failure log collection
- **Features**: Production-ready, secure secret management, highly configurable

## Configuration

### Environment Variables
The workflows create a `.env` file directly in the workflow (since `.env.testing` is not in git). Key settings:
- Database: MySQL 8.0 with `tournament_app_testing` database
- Cache/Session: Array drivers for testing
- Mail: Array driver (no actual emails sent)
- Environment: Set to `testing` for proper Laravel test behavior

### Using GitHub Secrets (Optional)
If you need to customize environment variables, you can use GitHub repository secrets:

1. Go to your repository → Settings → Secrets and variables → Actions
2. Add secrets like:
   - `APP_KEY` (if you want a specific key)
   - `DB_PASSWORD` (if you want a different password)
   - Any other sensitive configuration

3. Then modify the workflow to use them:
   ```yaml
   - name: Create testing environment file
     env:
       APP_KEY: ${{ secrets.APP_KEY }}
       DB_PASSWORD: ${{ secrets.DB_PASSWORD }}
     run: |
       cat > .env << EOF
       APP_KEY=${APP_KEY}
       DB_PASSWORD=${DB_PASSWORD}
       # ... rest of config
       EOF
   ```

### Database Setup
Tests run against a fresh MySQL database that's created for each workflow run:
- Database: `tournament_app_testing`
- Host: `127.0.0.1` (for main workflow) or `db` (for Docker workflow)
- Credentials: `root/root`

### Asset Building
Frontend assets are built using Vite during the workflow to ensure JavaScript/CSS dependencies are properly compiled.

## Which Pipeline Should You Use?

### For Production Use: `test-with-secrets.yml` (Currently Active) ✅
- **Use when**: You want production-ready CI/CD with configurable secrets
- **Pros**: Secure, configurable, comprehensive, runs automatically
- **Cons**: Requires GitHub secrets setup for advanced features
- **Status**: Currently enabled and running on push/PR

### For Simple Testing: `test.yml` (Currently Disabled)
- **Use when**: You want basic CI without secrets or special configuration
- **Pros**: Fast setup, no configuration needed, simple and reliable
- **Cons**: Less configurable, currently disabled
- **Status**: Commented out, can be enabled by uncommenting triggers

### For Docker Parity: `test-with-docker.yml` (Manual Only)
- **Use when**: You need testing environment identical to development
- **Pros**: Exact same environment as development, uses your Docker setup
- **Cons**: Slower, more complex, manual trigger only
- **Status**: Manual dispatch only

## Enabling/Disabling Pipelines

### Currently Active: `test-with-secrets.yml`
This pipeline runs automatically on every push/PR to `feature/serbian-app`. To disable:
1. Comment out the `push:` and `pull_request:` sections
2. Keep only `workflow_dispatch:` for manual triggers

### Currently Disabled: `test.yml`
This pipeline is commented out. To enable:
1. Uncomment the `push:` and `pull_request:` sections
2. You may want to disable the secrets pipeline to avoid running both

### Manual Only: `test-with-docker.yml`
This pipeline only runs on manual dispatch. To enable automatic runs:
1. Add the push/pull_request triggers:
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
- Verify the environment configuration is correct

### Asset Build Issues
- Ensure `package.json` has the correct `build` script
- Check that all npm dependencies are properly listed
- Verify Vite configuration is correct
- The workflow includes fallback assets creation if Vite build fails
- Check the "Build assets" and "Create fallback assets" steps for errors

### Environment File Issues
- The workflow creates `.env` from scratch (doesn't rely on `.env.testing`)
- Check the "Create testing environment file" step for any errors
- Verify all required environment variables are set

### Parallel Testing (Optional)
The workflows run tests sequentially by default. To enable parallel testing:

1. Add ParaTest to your composer.json:
   ```bash
   composer require --dev brianium/paratest
   ```

2. Update the workflow test command:
   ```yaml
   - name: Run tests
     run: php artisan test --env=testing --parallel
   ```

**Note**: Parallel testing can be faster but may cause issues with database transactions or shared resources.