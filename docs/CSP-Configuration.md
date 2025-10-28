# Content Security Policy (CSP) Configuration

## Overview
The E-AKSIDESA application uses a comprehensive Content Security Policy to maintain security while allowing the React frontend to function properly.

## Current CSP Policy

```
default-src 'self';
script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net;
style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://fonts.googleapis.com;
style-src-elem 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://fonts.googleapis.com;
font-src 'self' data: https://fonts.gstatic.com https://cdn.jsdelivr.net;
img-src 'self' data: http: https:;
connect-src 'self' http: https: ws: wss:;
media-src 'self';
object-src 'none';
child-src 'self';
worker-src 'self' blob:;
manifest-src 'self';
```

## Directive Explanations

### `script-src`
- `'self'`: Allow scripts from same origin
- `'unsafe-inline'`: Allow inline scripts (required for React)
- `'unsafe-eval'`: Allow eval() and similar (required for React/Vite HMR)
- `https://cdn.jsdelivr.net`: Allow Bootstrap Icons and other CDN scripts

### `style-src` & `style-src-elem`
- `'self'`: Allow stylesheets from same origin
- `'unsafe-inline'`: Allow inline styles (required for React CSS-in-JS)
- `https://cdn.jsdelivr.net`: Allow Bootstrap Icons CSS
- `https://fonts.googleapis.com`: Allow Google Fonts CSS

### `font-src`
- `'self'`: Allow fonts from same origin
- `data:`: Allow data URLs for fonts
- `https://fonts.gstatic.com`: Allow Google Fonts
- `https://cdn.jsdelivr.net`: Allow CDN fonts

### `img-src`
- `'self'`: Allow images from same origin
- `data:`: Allow data URLs for images
- `http: https:`: Allow images from any HTTP/HTTPS source

### `connect-src`
- `'self'`: Allow connections to same origin
- `http: https:`: Allow API calls to any HTTP/HTTPS endpoint
- `ws: wss:`: Allow WebSocket connections (for HMR)

### Security Restrictions
- `object-src 'none'`: Block all object/embed/applet elements
- `media-src 'self'`: Only allow media from same origin

## Common CSP Issues and Solutions

### Issue: `'unsafe-eval'` violations
**Cause**: React/Vite uses eval() for development builds and HMR
**Solution**: Added `'unsafe-eval'` to `script-src`

### Issue: `style-src-elem` violations
**Cause**: External stylesheets not explicitly allowed
**Solution**: Added explicit `style-src-elem` directive with CDN domains

### Issue: Font loading violations
**Cause**: Google Fonts and CDN fonts blocked
**Solution**: Added specific domains to `font-src`

## Modifying CSP

CSP is configured in two locations:
1. **Main Nginx Proxy**: `/docker/nginx/sites/default.conf`
2. **Laravel Nginx**: `/docker/laravel/nginx.conf`

Both configurations must be kept in sync.

### Steps to Update CSP:
1. Edit both nginx configuration files
2. Rebuild Laravel container: `docker compose build laravel`
3. Restart services: `docker compose restart laravel nginx`
4. Test with: `curl -I http://localhost`

## Testing CSP

### Browser Console
Check for CSP violations in browser developer tools console.

### Command Line Testing
```bash
# Check CSP headers
curl -I http://localhost

# Check API CSP headers
curl -I http://localhost/api/health
```

### Common Violations to Watch For
- Bootstrap Icons loading from CDN
- Google Fonts loading
- React inline styles
- Vite HMR WebSocket connections
- Dynamic script evaluation

## Security Considerations

While the current CSP allows necessary functionality, consider:
- Removing `'unsafe-eval'` in production builds
- Using nonces for inline scripts instead of `'unsafe-inline'`
- Restricting `img-src` to specific domains if possible
- Regular CSP policy reviews and updates
