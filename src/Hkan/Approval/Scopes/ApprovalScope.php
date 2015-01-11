<?php namespace Hkan\Approval\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ScopeInterface;

class ApprovalScope implements ScopeInterface {

	/**
	 * All of the extensions to be added to the builder.
	 *
	 * @var array
	 */
	protected $extensions = [ 'WithUnapproved', 'OnlyUnapproved' ];

	/**
	 * Apply the scope to a given Eloquent query builder.
	 *
	 * @param  \Illuminate\Database\Eloquent\Builder $builder
	 * @return void
	 */
	public function apply(Builder $builder)
	{
		$model = $builder->getModel();

		$builder->where($model->getApprovalColumn(), true);

		$this->extend($builder);
	}

	/**
	 * Remove the scope from the given Eloquent query builder.
	 *
	 * @param  \Illuminate\Database\Eloquent\Builder $builder
	 * @return void
	 */
	public function remove(Builder $builder)
	{
		$column = $builder->getModel()->getApprovalColumn();

		$query = $builder->getQuery();

		foreach ((array)$query->wheres as $key => $where)
		{
			// If the where clause is a soft delete date constraint, we will remove it from
			// the query and reset the keys on the wheres. This allows this developer to
			// include deleted model in a relationship result set that is lazy loaded.
			if ($this->isApprovalColumn($where, $column))
			{
				unset($query->wheres[$key]);

				$bindings = array_get($query->getRawBindings(), 'where');

				unset($bindings[$key]);

				$query->setBindings(array_values($bindings));

				$query->wheres = array_values($query->wheres);
			}
		}
	}

	/**
	 * Extend the query builder with the needed functions.
	 *
	 * @param  \Illuminate\Database\Eloquent\Builder $builder
	 * @return void
	 */
	public function extend(Builder $builder)
	{
		foreach ($this->extensions as $extension)
		{
			$this->{"add{$extension}"}($builder);
		}
	}

	/**
	 * Add the with-trashed extension to the builder.
	 *
	 * @param  \Illuminate\Database\Eloquent\Builder $builder
	 * @return void
	 */
	protected function addWithUnapproved(Builder $builder)
	{
		$builder->macro('withUnapproved', function (Builder $builder)
		{
			$this->remove($builder);

			return $builder;
		});
	}

	/**
	 * Add the only-trashed extension to the builder.
	 *
	 * @param  \Illuminate\Database\Eloquent\Builder $builder
	 * @return void
	 */
	protected function addOnlyUnapproved(Builder $builder)
	{
		$builder->macro('onlyUnapproved', function (Builder $builder)
		{
			$this->remove($builder);

			$builder->getQuery()->where($builder->getModel()->getApprovalColumn(), false);

			return $builder;
		});
	}

	protected function isApprovalColumn($where, $column)
	{
		return $where['column'] == $column;
	}

}
