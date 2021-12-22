## ldaply

This folder contains all defined tasks and helper classes for scheduled tasks. <br>

You can use the built-in command for creating new tasks:
```
cd path_to_root

php artisan scheduledtasks:make {name}
```

**Note:** Use **--core** to create a core task:<br />
```
php artisan scheduledtasks:make {name} --core
```