# Field Types

Field types determine how your model attributes are stored, validated, and cast. CRUD Templates for Laravel supports a wide range of field types for various use cases.

## Basic Syntax

Fields are defined using the following format:

```bash
fieldName:fieldType
```

For nullable fields, add a `?` after the field name:

```bash
fieldName?:fieldType
```

Multiple fields are separated by commas:

```bash
--fields="name:string,age:integer,bio?:text"
```

## Supported Field Types

### String

Creates a varchar column with a maximum of 255 characters.

**Syntax:**
```bash
--fields="name:string"
```

**Migration:**
```php
$table->string('name');
```

**Validation:**
```php
'name' => 'required|string|max:255'
```

**Model Cast:** None (default string handling)

**Example:**
```bash
php artisan crud:generate User --fields="first_name:string,last_name:string,email:string"
```

---

### Integer

Creates an integer column for whole numbers.

**Syntax:**
```bash
--fields="age:integer"
```

**Migration:**
```php
$table->integer('age');
```

**Validation:**
```php
'age' => 'required|integer'
```

**Model Cast:** None (default integer handling)

**Example:**
```bash
php artisan crud:generate Product --fields="stock:integer,views:integer"
```

---

### Decimal

Creates a decimal column for precise numeric values (e.g., prices, measurements).

**Syntax:**
```bash
--fields="price:decimal"
```

**Migration:**
```php
$table->decimal('price', 8, 2);
```

**Validation:**
```php
'price' => 'required|numeric'
```

**Model Cast:**
```php
'price' => 'decimal:2'
```

**Example:**
```bash
php artisan crud:generate Product --fields="price:decimal,weight:decimal"
```

---

### Date

Creates a date column (without time).

**Syntax:**
```bash
--fields="birth_date:date"
```

**Migration:**
```php
$table->date('birth_date');
```

**Validation:**
```php
'birth_date' => 'required|date'
```

**Model Cast:**
```php
'birth_date' => 'immutable_date'
```

**Example:**
```bash
php artisan crud:generate Employee --fields="birth_date:date,hire_date:date"
```

---

### DateTime

Creates a datetime column with both date and time.

**Syntax:**
```bash
--fields="published_at:datetime"
```

**Migration:**
```php
$table->dateTime('published_at');
```

**Validation:**
```php
'published_at' => 'required|date'
```

**Model Cast:**
```php
'published_at' => 'immutable_datetime'
```

**Example:**
```bash
php artisan crud:generate Post --fields="published_at:datetime,scheduled_at?:datetime"
```

---

### Text

Creates a text column for longer content (no length limit).

**Syntax:**
```bash
--fields="description:text"
```

**Migration:**
```php
$table->text('description');
```

**Validation:**
```php
'description' => 'required|string'
```

**Model Cast:** None

**Example:**
```bash
php artisan crud:generate Article --fields="content:text,excerpt?:text"
```

---

### Boolean

Creates a boolean column for true/false values.

**Syntax:**
```bash
--fields="is_active:boolean"
```

**Migration:**
```php
$table->boolean('is_active');
```

**Validation:**
```php
'is_active' => 'required|boolean'
```

**Model Cast:**
```php
'is_active' => 'boolean'
```

**Example:**
```bash
php artisan crud:generate User --fields="is_active:boolean,is_verified:boolean"
```

---

### Enum

Creates a string column with enum casting (requires PHP enum class).

**Syntax:**
```bash
--fields="status:enum:StatusEnum"
```

**Migration:**
```php
$table->string('status');
```

**Validation:**
None (validation handled by enum)

**Model Cast:**
```php
'status' => StatusEnum::class
```

**Example:**
```bash
php artisan crud:generate Order --fields="status:enum:OrderStatus,priority:enum:Priority"
```

::: warning Enum Requirements
You must create the enum class before generating the CRUD. The enum class should exist in your application.
:::

---

### JSON

Creates a JSON column for storing structured data.

**Syntax:**
```bash
--fields="metadata:json"
```

**Migration:**
```php
$table->json('metadata');
```

**Validation:**
```php
'metadata' => 'required|array'
```

**Model Cast:**
```php
'metadata' => 'array'
```

**Example:**
```bash
php artisan crud:generate Product --fields="attributes:json,settings?:json"
```

---

## Nullable Fields

Add a `?` after the field name to make it nullable:

```bash
php artisan crud:generate User --fields="name:string,phone?:string,bio?:text"
```

This will:
- Make the column nullable in the migration
- Remove `required` from validation rules
- Allow `null` values in the model

## Complete Example

Here's a comprehensive example using multiple field types:

```bash
php artisan crud:generate Product \
  --fields="name:string,sku:string,description:text,price:decimal,stock:integer,is_active:boolean,published_at?:datetime,metadata?:json"
```

This generates a product CRUD with:
- String fields for name and SKU
- Text field for description
- Decimal field for price
- Integer field for stock quantity
- Boolean field for active status
- Optional datetime for publication date
- Optional JSON field for additional metadata

## Next Steps

- Learn about [Relationships](/guide/relationships) to connect models
- Explore [Generate from table](/guide/table-introspection) for existing tables
- Understand how to create [Custom Field Types](/templates/customizing-field-types)

