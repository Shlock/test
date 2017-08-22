<?php namespace YiZan\Relations;

use Illuminate\Database\Eloquent\Relations\HasOneOrMany;
use Illuminate\Database\Eloquent\Collection;

class HasManyCount extends HasOneOrMany {

	/**
	 * Get the results of the relationship.
	 *
	 * @return mixed
	 */
	public function getResults()
	{
		return $this->query->select($this->foreignKey)->selectRaw('COUNT(*) AS row_count')->groupBy($this->foreignKey)->get();
	}

	protected function getRelationValue(array $dictionary, $key, $type)
	{
		$value = reset($dictionary[$key]);
		return $value->row_count;
	}

	public function initRelation(array $models, $relation)
	{
		foreach ($models as $model) {
			$model->setCountRelation($relation, null);
		}

		return $models;
	}

	/**
	 * Match the eagerly loaded results to their parents.
	 *
	 * @param  array   $models
	 * @param  \Illuminate\Database\Eloquent\Collection  $results
	 * @param  string  $relation
	 * @return array
	 */
	public function match(array $models, Collection $results, $relation)
	{
		return $this->matchOne($models, $results, $relation);
	}

	protected function matchOneOrMany(array $models, Collection $results, $relation, $type)
	{
		$dictionary = $this->buildDictionary($results);
		// Once we have the dictionary we can simply spin through the parent models to
		// link them up with their children using the keyed dictionary to make the
		// matching very convenient and easy work. Then we'll just return them.
		foreach ($models as $model)
		{
			$key = $model->getAttribute($this->localKey);
			if (isset($dictionary[$key]))
			{
				$value = $this->getRelationValue($dictionary, $key, $type);
				$model->setCountRelation($relation, $value);
			}
		}

		return $models;
	}

	public function __call($method, $parameters)
	{
		if ($method == 'get') {
			$this->query->select($this->foreignKey)->selectRaw('COUNT(*) AS row_count')->groupBy($this->foreignKey);
		}
		$result = call_user_func_array(array($this->query, $method), $parameters);
		if ($result === $this->query) return $this;

		return $result;
	}

}
