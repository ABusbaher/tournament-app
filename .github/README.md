# GitHub Actions CI/CD Pipeline

This directory contains the CI/CD pipeline configuration for the Tournament App.

## Pipeline Overview

The `ci-pipeline.yml` workflow provides a complete CI/CD pipeline that automatically:

- **Builds** the application with all dependencies
- **Configures** the testing environment  
- **Migrates** the database schema
- **Tests** the application with PHPUnit
- **Reports** results and collects failure logs

## Triggers

The pipeline runs automatically on:
- **Push** to `feature/serbian-app` branch
- **Pull Request** to `feature/serbian-app` branch
- **Manual dispatch** (can be triggered manually from GitHub Actions tab)

## Pipeline Steps

### 1. Environment Setup
- Ubuntu latest with PHP 8.1 and MySQL 8.0
- Node.js 18 for frontend asset building
- All required PHP extensions (pdo, mysql, gd, etc.)

### 2. Application Configuration
- Creates `.env` file with testing configuration
- Supports GitHub repository secrets for customization
- Configures database connection and application settings

### 3. Dependency Installation
- Installs PHP dependencies with Composer
- Installs Node.js dependencies with npm
- Optimizes autoloader for better performance

### 4. Asset Building
- Builds frontend assets with Vite
- Compiles CSS and JavaScript files
- Ensures all assets are available for testing

### 5. Database Setup
- Creates fresh test database
- Runs all database migrations
- Ensures clean state for each test run

### 6. Test Execution
- Runs complete PHPUnit test suite
- Tests all application features and functionality
- Provides detailed test results

### 7. Result Reporting
- Uploads failure logs and artifacts if tests fail
- Provides detailed feedback on pipeline status

## Configuration

### Environment Variables
The pipeline creates a complete `.env` file with testing-appropriate settings:
- **Database**: Fresh MySQL database for each run
- **Cache/Session**: Array drivers (no persistence needed)
- **Mail**: Array driver (no actual emails sent)
- **Environment**: Set to `testing` for proper Laravel behavior

### GitHub Secrets (Optional)
You can customize the pipeline using GitHub repository secrets:

1. Go to **Repository Settings** → **Secrets and variables** → **Actions**
2. Add any of these optional secrets:
   - `APP_KEY` - Custom application encryption key
   - `DB_PASSWORD` - Custom database password  
   - `MAIL_FROM_ADDRESS` - Custom email sender address

3. The pipeline will use these secrets with sensible fallbacks:
   ```yaml
   APP_KEY: ${{ secrets.APP_KEY }}           # Auto-generated if not provided
   DB_PASSWORD: ${{ secrets.DB_PASSWORD || 'root' }}
   MAIL_FROM_ADDRESS: ${{ secrets.MAIL_FROM_ADDRESS || 'hello@example.com' }}
   ```

## Pipeline Control

### Disable Pipeline
To temporarily disable the pipeline:
1. Comment out the `push:` and `pull_request:` sections
2. Keep `workflow_dispatch:` for manual triggers only

### Enable Manual-Only Mode
```yaml
on:
  # push:
  #   branches: [ feature/serbian-app ]
  # pull_request:
  #   branches: [ feature/serbian-app ]
  workflow_dispatch:
```

## Troubleshooting

### Test Failures
- Check the **"Execute test suite"** step output for specific failures
- Download failure artifacts automatically uploaded by the pipeline
- Ensure your local tests pass before pushing

### Database Issues
- Pipeline creates a fresh database for each run
- Check that migrations run successfully in the logs
- Verify database configuration in environment setup

### Asset Build Issues
- Check the **"Build frontend assets"** step for Vite errors
- Ensure `package.json` has correct build script
- Verify all npm dependencies are properly listed

### Environment Configuration
- Pipeline creates `.env` from scratch (no dependency on local files)
- Check the **"Configure application environment"** step
- Verify all required environment variables are set

### Secret Management
- Secrets are optional - pipeline works without them
- Check secret names match exactly (case-sensitive)
- Verify secrets are set in repository settings, not personal settings

## Performance

The pipeline typically completes in **3-5 minutes** with these approximate timings:
- Environment setup: ~30 seconds
- Dependency installation: ~60 seconds  
- Asset building: ~30 seconds
- Database setup: ~15 seconds
- Test execution: ~60-180 seconds (depends on test count)

## Security

- No sensitive data is stored in the workflow file
- Database credentials are temporary and isolated
- Optional secrets provide secure configuration management
- All artifacts are automatically cleaned up after the pipeline