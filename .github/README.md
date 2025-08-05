# GitHub Actions Workflows

This directory contains GitHub Actions workflows for the Tournament App.

## Available Workflows

### 1. `test.yml` - Main Test Workflow (Recommended) ✅
- **Trigger**: Pushes and Pull Requests to `feature/serbian-app` branch
- **Environment**: Ubuntu with PHP 8.1 and MySQL 8.0 service
- **Configuration**: Creates `.env` file directly in workflow
- **Features**:
  - Runs all PHPUnit tests
  - Builds frontend assets with Vite
  - Uses parallel testing for faster execution
  - Uploads test artifacts on failure
  - No external dependencies or secrets required

### 2. `test-with-docker.yml` - Docker-based Testing (Alternative)
- **Trigger**: Manual dispatch only (disabled by default)
- **Environment**: Uses your existing Docker Compose setup
- **Configuration**: Creates `.env` file for Docker environment
- **Features**:
  - Tests in an environment closer to production
  - Uses the same Docker containers as development
  - More complex setup but higher fidelity

### 3. `test-with-secrets.yml` - Secrets-based Testing (Advanced)
- **Trigger**: Manual dispatch only (disabled by default)
- **Environment**: Ubuntu with configurable secrets
- **Configuration**: Uses GitHub repository secrets for sensitive data
- **Features**:
  - Allows customization of environment variables via secrets
  - Better security for production-like configurations
  - Fallback values for missing secrets
  - Demonstrates best practices for secret management

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

## Which Workflow Should You Use?

### For Most Cases: `test.yml` (Recommended) ✅
- **Use when**: You want simple, fast, reliable testing
- **Pros**: Fast setup, no configuration needed, runs automatically
- **Cons**: Environment differs slightly from your Docker development setup

### For Docker Parity: `test-with-docker.yml`
- **Use when**: You need testing environment identical to development
- **Pros**: Exact same environment as development, uses your Docker setup
- **Cons**: Slower, more complex, requires Docker Compose knowledge

### For Advanced Configuration: `test-with-secrets.yml`
- **Use when**: You need custom environment variables or secrets
- **Pros**: Flexible configuration, secure secret management, production-ready
- **Cons**: Requires manual setup of GitHub secrets

## Enabling/Disabling Workflows

### Main Workflow (test.yml)
This runs automatically on every push/PR to `feature/serbian-app`. To disable:
1. Change the `on:` section to `workflow_dispatch:`
2. Or delete the file entirely

### Alternative Workflows
Both `test-with-docker.yml` and `test-with-secrets.yml` are disabled by default. To enable either:
1. Change `workflow_dispatch:` to:
   ```yaml
   push:
     branches: [ feature/serbian-app ]
   pull_request:
     branches: [ feature/serbian-app ]
   ```
2. For the secrets workflow, also set up the required GitHub secrets in your repository settings

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

### Environment File Issues
- The workflow creates `.env` from scratch (doesn't rely on `.env.testing`)
- Check the "Create testing environment file" step for any errors
- Verify all required environment variables are set