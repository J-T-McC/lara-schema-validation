<?php

namespace Tests\Stubs\Traits;

use Illuminate\Database\Eloquent\Collection;
use RuntimeException;

trait StubHelper
{
    public array $fixture = [];

    public function loadFixture(string $stub): self
    {
        // get the fixture json file from the current path/fixtures folder
        $fixture = __DIR__.'/../Fixtures/'.$stub;

        if (! file_exists($fixture)) {
            throw new RuntimeException('Fixture not found: '.$fixture);
        }

        $json = file_get_contents($fixture);

        if ($json === false) {
            throw new RuntimeException('Failed to read fixture: '.$fixture);
        }

        $this->fixture = json_decode($json, true);

        return $this;
    }

    public function setFixture(array $fixture): self
    {
        $this->fixture = $fixture;

        return $this;
    }

    public function hydrateFromFixture(): self
    {
        foreach ($this->fixture as $key => $value) {
            $this->setFixtureProperty($key, $value);
        }

        return $this;
    }

    private function setFixtureProperty(string $key, mixed $value): void
    {
        // If the method exists, we assume it's a relation
        if (method_exists($this, $key)) {
            $relation = $this->{$key}();

            $relatedClass = method_exists($relation, 'getChild')
                ? $relation->getChild()
                : null;

            if (! class_exists($relatedClass)) {
                return;
            }

            if (is_array($value) && array_is_list($value)) {
                // Handle hasMany-style relation (array of models)
                $this->{$key} = Collection::make($value)->map(fn ($item) => (new $relatedClass)->setFixture($item)->hydrateFromFixture());
            } elseif (is_array($value)) {
                // Handle hasOne-style relation
                $this->{$key} = (new $relatedClass)->setFixture($value)->hydrateFromFixture();
            }

            return;
        }

        // Fallback: direct assignment
        $this->{$key} = $value;
    }
}
