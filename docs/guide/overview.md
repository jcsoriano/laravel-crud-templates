# Overview

The CRUD Templates for Laravel package allows you to generate fully functioning CRUD features with a single command. It'll generate all the files you need: controllers, models, policies, requests, resources, migrations, factories, and even tests!

It's extremely customizable, so you can adapt it to your project's conventions perfectly.

### Key Features

- **Complete CRUD Generation**: Generate all necessary files (controllers, models, policies, requests, resources, migrations, factories, tests) with a single command
- **Fully Customizable**: Modify stubs, generators, field types, and printers to match your project's conventions
- **Multiple Templates**: Use the built-in API template or create your own for different scenarios
- **Smart Field Types**: Support for strings, text, numbers, dates, enums, relationships, and more
- **Automatic Relationships**: Generates relationship methods, foreign keys, and resource transformations
- **Built-in Authorization**: Includes policy generation with proper scoping
- **Test Generation**: Creates feature tests with proper assertions
- **Code Formatting**: Automatically formats generated files using Laravel Pint

## Why Use CRUD Templates?

1. **Consistency**: Every generated file follows the same conventions and structure
2. **Speed**: Finish CRUD features in seconds instead of hours
3. **Completeness**: Never forget to create a request class, policy, or test again
4. **Customization**: Adapt the generator to your exact project needs
5. **AI-Friendly**: Generate a solid foundation that AI can work with more effectively

## How the Documentation is Structured

This documentation is organized into four main sections:

### 1. Getting Started
Start here if you're new to CRUD Templates:
- **[Overview](/guide/overview)** - You are here! Introduction and documentation guide
- **[Installation](/guide/installation)** - How to install and set up the package
- **[Quick Start](/guide/quick-start)** - Generate your first CRUD in minutes

### 2. Available Templates
Explore and create templates:
- **[API Template](/templates/api)** - Documentation for the built-in RESTful API template
- **[Creating Your Own Template](/templates/custom)** - Build custom templates for specific use cases

### 3. Using Templates
Learn how to use the generator effectively:
- **[Field Types](/guide/field-types)** - Complete list of available field types and their syntax
- **[Relationships](/guide/relationships)** - How to define and generate model relationships
- **[Generate from Schema](/guide/generate-from-schema)** - Generate CRUD from existing database tables

### 4. Customizing Templates
Make the generator work exactly how you want:
- **[Customizing Stubs](/templates/customizing-stubs)** - Modify the template files used for generation
- **[Customizing Generators](/templates/customizing-generators)** - Create or override file generators
- **[Customizing Field Types](/templates/customizing-field-types)** - Add custom field types with your own logic
- **[Customizing Printers](/templates/customizing-printers)** - Customize how code snippets are generated

## Quick Example

Here's a taste of what you can do with CRUD Templates:

```bash
php artisan crud:generate Content/Post \
--template=api \
--fields="title:string,content:text,published_at:datetime,category:belongsTo,comments:hasMany,status:enum:PublishStatus" \
--options="scope:user"
```

This single command generates:
- Controller with RESTful methods
- Model with fillable fields, casts, and relationships
- Authorization policy scoped to users
- Store and Update request classes with validation
- API Resource for response transformation
- Database migration with foreign keys
- Model factory for testing
- Feature tests with assertions
- API routes automatically registered

## Next Steps

Ready to get started? Here's what to do next:

1. **[Install the package](/guide/installation)** - Set up CRUD Templates in your Laravel project
2. **[Follow the Quick Start guide](/guide/quick-start)** - Generate your first CRUD feature
3. **[Explore the API Template](/templates/api)** - Understand the generated structure
4. **[Learn about Field Types](/guide/field-types)** - Discover all available field types
5. **[Customize to your needs](/templates/customizing-stubs)** - Adapt the generator to your project

## Getting Help

If you encounter any issues or have questions:

- Check the [Troubleshooting](/troubleshooting) page for common issues
- Report bugs on [GitHub Issues](https://github.com/jcsoriano/laravel-crud-templates/issues)
- Contribute improvements via [Pull Requests](https://github.com/jcsoriano/laravel-crud-templates/pulls)

Let's build something amazing together!

