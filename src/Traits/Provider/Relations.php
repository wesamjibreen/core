<?php

namespace Core\Traits\Provider;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait Relations
{
    /**
     * Registering all relationships functions
     *
     * @return void
     * @author WeSSaM
     */
    public function relationsMacro(): void
    {
        $this->hasManyMacro();
        $this->belongsToManyMacro();
    }

    /**
     * Inject sync method for has many relation
     *
     * @return void
     * @author WeSSaM
     */
    public function hasManyMacro(): void
    {
        HasMany::macro('hasManySync', function ($data, $deleting = true) {
            $changes = [
                'created' => [], 'deleted' => [], 'updated' => [],
            ];

            /**
             * Cast the given keys to integers if they are numeric and string otherwise.
             *
             * @param array $keys
             *
             * @return array
             */
            $castKeys = function (array $keys) {
                return (array)array_map(function ($v) {
                    return is_numeric($v) ? (int)$v : (string)$v;
                }, $keys);
            };


            $relatedKeyName = $this->related->getKeyName();

            $getCompositeKey = function ($row) use ($relatedKeyName) {
                $keys = [];
                foreach ((array)$relatedKeyName as $k) {
                    $keys[] = data_get($row, $k);
                }
                return join('|', $keys);
            };

            // First we need to attach any of the associated models that are not currently
            // in the child entity table. We'll spin through the given IDs, checking to see
            // if they exist in the array of current ones, and if not we will insert.
            $current = $this->newQuery()->get($relatedKeyName)->map($getCompositeKey)->toArray();

//            dd($current);
            // Separate the submitted data into "update" and "new"
            $updateRows = [];
            $newRows = [];

            foreach ($data as $row) {
                $key = $getCompositeKey($row);
                // We determine "updateable" rows as those whose $relatedKeyName (usually 'id') is set, not empty, and
                // match a related row in the database.
                if (!empty($key) && in_array($key, $current)) {
                    $updateRows[$key] = $row;
                } else {
                    $newRows[] = $row;
                }
            }


            // Next, we'll determine the rows in the database that aren't in the "update" list.
            // These rows will be scheduled for deletion.  Again, we determine based on the relatedKeyName (typically 'id').
            $updateIds = array_keys($updateRows);

            if ($deleting) {
                $deleteIds = [];
                foreach ($current as $currentId) {
                    if (!in_array($currentId, $updateIds)) {
                        $deleteIds[$currentId] = array_combine((array)$relatedKeyName, explode('|', $currentId));
                    }
                }

                // Delete any non-matching rows
                if (count($deleteIds) > 0) {
                    /**
                     * @var \Illuminate\Database\Query\Builder $q
                     */
                    $q = $this->newQuery();
                    $q->where(function ($q) use ($relatedKeyName, $deleteIds) {
                        foreach ($deleteIds as $row) {
                            $q->where(function ($q) use ($relatedKeyName, $row) {
                                foreach ((array)$relatedKeyName as $key) {
                                    $q->where($key, $row[$key]);
                                }
                            }, null, null, 'or');
                        }
                    });
                    $q->delete();

                    $changes['deleted'] = $castKeys(array_keys($deleteIds));
                }
            }

            // Update the updatable rows
            foreach ($updateRows as $id => $row) {
                $q = $this->getRelated()::query();
                $q->where("id", $id);
//                dd($row);
//                    ->where("branch_id",$row["branch_id"]);
//                foreach ( (array)$relatedKeyName as $key ) {
//                    $q->where( $key, $row[$key] );
//                }
//                dd($q,$row);
                unset($row['created_at']);
                $row['updated_at'] = Carbon::now();
                $q->update($row);
            }

            $changes['updated'] = $castKeys($updateIds);

            // Insert the new rows
            $newIds = [];
            foreach ($newRows as $row) {
                $newModel = $this->create($row);
                $newIds[] = $getCompositeKey($newModel);
            }

            $changes['created'] = $castKeys($newIds);

//            dd($changes);
            return $changes;
        });
    }

    /**
     * @return void
     * @author WeSSaM
     */
    public function belongsToManyMacro(): void
    {
        BelongsToMany::macro("belongsToManySync", function ($data, $deleting = true) {
            $sanitizedArr = [];

//            (  new BelongsToMany)->getRelatedPivotKeyName()
            foreach ($data as $item) {
                $row = $item;
                $id = isset($row[$this->getRelatedPivotKeyName()]) ? $row[$this->getRelatedPivotKeyName()] : (isset($row['id']) ? $row['id'] : null);
                if (isset($row['id'])) unset($row['id']);
                $sanitizedArr[$id] = $row;
            }

            $this->sync($sanitizedArr);
        });
    }
}
