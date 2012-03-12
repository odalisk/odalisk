Installation
============

Initialize the project
-----------------

```bash
# Clone the project
git clone git@github.com:odalisk/odalisk.git path/to/your/project/odalisk

# Install the vendors
php composer.phar install
```

Configure the database
----------------------
```bash
cd path/to/your/project/odalisk/app
mv kernel.custom.yml.dist kernel.custom.yml
# Edit kernel.custom.yml and add your database prefs.
vi kernel.custom.yml

# You are now ready to create the database
cd ..
./console doctrine:database:create
./console doctrine:schema:create
```

Now just point your virtualhost to ```bash path/to/your/project/odalisk/web ``` and you're good to go.

Some useful pointers to get started
-----------------------------------

- [Symfony2](http://symfony.com)
- [KnpRadBundle](http://rad.knplabs.com/)
- [Doctrine2](http://www.doctrine-project.org/)