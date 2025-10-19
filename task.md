Revisions:

1. Add return types to relations
2. When `--table` is used, don't print a migration file
3. Add ability to automatically detect polymorphic fields (`_type` and `_id` pairs)
4. Rename crud.{something}.stub to api.{something}.stub, update all references accordingly
5. Check if the package already supports publishing the stubs. If it doesn't, add support for it. 
6. Add a `--force` option. Don't overwrite files by default unless `--force` is set
7. Update all `factory` method in field types to return Output. Update all usages accordingly. Add the enumClass to the Output as the second argument (namespace)
8. In all the changes above, update the documentation if needed