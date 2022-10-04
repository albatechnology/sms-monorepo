<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## API specification
`vyuldashev/laravel-openapi` is used to generate open API specification based on PHP annotation.
As the package only generate the json file but does not save it, we create our custom artisan command to save the generated file.

Run `php artisan openapi:save` to regenerate this file. 
Note that this package is very sensitive to invalid route (web.php included).
Any non-existent controller method or missing namespace uses will cause an error, and in turn fail the CI/CD.
So it is a good idea to always run this command everytime before a push, even when you are not editing API feature.

## User

### User Type
The user property `type` is used to determine the type of access that should 
be given to the user.
- `default` is user without any special access on the mobil app. This user should
   only have access to backend CMS.
- `sales` is the main target user for the mobile app.
- `supervisor` each user can be assigned to a supervisor. Supervisor would
   have unique feature access on the mobile app compared to sales.
- `director` special type of user that have access to all companies.

