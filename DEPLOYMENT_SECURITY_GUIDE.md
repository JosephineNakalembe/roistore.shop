# ROI Store - Deployment Security Guide

## Overview
This guide ensures your ROI Store platform is secure, seamless, and protects user data when deployed to production.

## Critical Security Changes Made

### 1. Session Security
- **Enabled session encryption**: All session data is now encrypted before storage
- **Secure cookies**: Cookies only transmit over HTTPS in production
- **HTTP-only cookies**: Prevents JavaScript access to session cookies (XSS protection)
- **Same-site policy**: Protects against CSRF attacks

### 2. Authentication Improvements
- **Removed hardcoded credentials**: Admin credentials are no longer hardcoded in the application
- **Seamless navigation**: Users can easily switch between login and registration
- **Role-based access**: Proper admin/user role detection via database

### 3. Security Headers
- **CSRF token**: Added to all forms automatically
- **Content Security Policy**: Upgrades insecure requests to HTTPS

## Deployment Checklist

### Environment Configuration

#### For Local Development (.env)
```env
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost
SESSION_SECURE_COOKIE=false  # Disabled for local HTTP
```

#### For Production (.env)
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com  # Must use HTTPS
SESSION_SECURE_COOKIE=true     # Enabled for HTTPS
```

### Database Setup

1. **Install PostgreSQL driver** (if not already installed):
   ```bash
   # Ubuntu/Debian
   sudo apt-get install php-pgsql
   
   # Windows (XAMPP)
   # Ensure php_pgsql extension is enabled in php.ini
   ```

2. **Run migrations**:
   ```bash
   php artisan migrate
   ```
   
   This will create the `sessions` table required for encrypted session storage.

3. **Create admin user** (run once):
   ```bash
   php artisan tinker
   ```
   Then execute:
   ```php
   App\Models\User::create([
       'name' => 'Administrator',
       'email' => 'admin@yourdomain.com',
       'password' => Hash::make('your-secure-password'),
       'role' => 'admin',
       'status' => 'active',
   ]);
   ```

### SSL/HTTPS Configuration

#### Option 1: Let's Encrypt (Recommended)
```bash
# Install Certbot
sudo apt-get install certbot python3-certbot-apache

# Get certificate
sudo certbot --apache -d yourdomain.com -d www.yourdomain.com
```

#### Option 2: Cloudflare (Free)
1. Sign up at https://cloudflare.com
2. Add your domain
3. Change nameservers as instructed
4. Enable "Full (strict)" SSL/TLS mode

### Production .env Settings

```env
APP_NAME="ROI Store"
APP_ENV=production
APP_KEY=base64:YOUR_GENERATED_KEY_HERE
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Database
DB_CONNECTION=pgsql
DB_HOST=your-db-host
DB_PORT=5432
DB_DATABASE=roi_website
DB_USERNAME=roi_website_user
DB_PASSWORD=your-secure-db-password

# Session Security (CRITICAL)
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=true
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax

# Mail (configure for production)
MAIL_MAILER=smtp
MAIL_HOST=smtp.your-provider.com
MAIL_PORT=587
MAIL_USERNAME=your-email@domain.com
MAIL_PASSWORD=your-email-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="ROI Store"
```

### Security Best Practices

1. **Never commit .env to version control**
   - Already in .gitignore
   - Use environment variables in production

2. **Generate new APP_KEY for production**:
   ```bash
   php artisan key:generate
   ```

3. **Set proper file permissions**:
   ```bash
   chmod -R 755 storage bootstrap/cache
   chmod -R 644 .env
   ```

4. **Enable HTTPS redirect** in your web server configuration

5. **Regular backups**:
   - Database backups daily
   - Store backups securely off-site

6. **Keep dependencies updated**:
   ```bash
   composer update --no-dev
   npm update --production
   ```

### User Data Protection

The following measures protect user data:

1. **Password Hashing**: All passwords are hashed using bcrypt (12 rounds)
2. **Session Encryption**: Session data is encrypted in the database
3. **CSRF Protection**: All forms protected against CSRF attacks
4. **SQL Injection Prevention**: Using Laravel's query builder/Eloquent
5. **XSS Protection**: HTTP-only cookies and input sanitization
6. **Role-based Access**: Proper middleware prevents unauthorized access

### Troubleshooting

#### "Site is not safe" warning
- Ensure HTTPS is properly configured
- Check that SSL certificate is valid and not expired
- Verify `SESSION_SECURE_COOKIE=true` in production
- Ensure `APP_URL` uses `https://` not `http://`

#### Session not persisting
- Verify sessions table exists: `php artisan migrate`
- Check database connection
- Ensure SESSION_DRIVER=database
- Clear cache: `php artisan cache:clear`

#### Login issues after deployment
- Clear all caches: `php artisan optimize:clear`
- Verify APP_KEY is set correctly
- Check file permissions on storage directory

### Testing Deployment

1. **Test registration flow**:
   - Visit `/register`
   - Create a test account
   - Verify automatic login and redirect to shop

2. **Test login flow**:
   - Visit `/login`
   - Sign in with test account
   - Verify redirect to appropriate dashboard

3. **Test admin access**:
   - Login with admin credentials
   - Verify redirect to `/admin` dashboard
   - Verify admin cannot access buyer areas

4. **Test security**:
   - Verify HTTPS is active (padlock icon in browser)
   - Check cookies are marked as "Secure" and "HttpOnly"
   - Test that session persists after page refresh

## Support

For issues or questions, refer to the main README.md or contact the development team.