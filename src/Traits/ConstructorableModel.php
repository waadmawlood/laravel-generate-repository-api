<?php

namespace Waad\Repository\Traits;

trait ConstructorableModel
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        if(property_exists($this, 'connection_override')){
            unset($this->connection_override);
        }

        if(property_exists($this, 'hidden_override')){
            unset($this->hidden_override);
        }

        if(property_exists($this, 'casts_override')){
            unset($this->casts_override);
        }

        if(property_exists($this, 'guarded_override')){
            unset($this->guarded_override);
        }

        if(property_exists($this, 'fillable_override')){
            unset($this->fillable_override);
        }

        if(property_exists($this, 'table_override')){
            unset($this->table_override);
        }

        if(property_exists($this, 'primary_override')){
            unset($this->primary_override);
        }

        if(property_exists($this, 'timestamps_override')){
            unset($this->timestamps_override);
        }

        if(property_exists($this, 'incrementing_override')){
            unset($this->incrementing_override);
        }

        if(property_exists($this, 'keyType_override')){
            unset($this->keyType_override);
        }

        if(property_exists($this, 'with_override')){
            unset($this->with_override);
        }

        if(property_exists($this, 'withCount_override')){
            unset($this->withCount_override);
        }

        if(property_exists($this, 'appends_override')){
            unset($this->appends_override);
        }
    }

    public function initializeConstructorableModel()
    {
        if(property_exists($this, 'connection_override')){
            $this->connection = $this->connection_override;
        }

        if(property_exists($this, 'hidden_override')){
            $this->hidden = $this->hidden_override;
        }

        if(property_exists($this, 'casts_override')){
            $this->casts = $this->casts_override;
        }

        if(property_exists($this, 'guarded_override')){
            $this->guarded = $this->guarded_override;
        }

        if(property_exists($this, 'fillable_override')){
            $this->fillable = $this->fillable_override;
        }

        if(property_exists($this, 'table_override')){
            $this->table = $this->table_override;
        }

        if(property_exists($this, 'primary_override')){
            $this->primary = $this->primary_override;
        }

        if(property_exists($this, 'timestamps_override')){
            $this->timestamps = $this->timestamps_override;
        }

        if(property_exists($this, 'incrementing_override')){
            $this->incrementing = $this->incrementing_override;
        }

        if(property_exists($this, 'keyType_override')){
            $this->keyType = $this->keyType_override;
        }

        if(property_exists($this, 'with_override')){
            $this->with = $this->with_override;
        }

        if(property_exists($this, 'withCount_override')){
            $this->withCount = $this->withCount_override;
        }

        if(property_exists($this, 'appends_override')){
            $this->appends = $this->appends_override;
        }
    }
}
