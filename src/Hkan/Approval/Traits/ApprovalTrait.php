<?php namespace Hkan\Approval\Traits;

use Hkan\Approval\Scopes\ApprovalScope;

/**
 * Trait ApprovalTrait
 * @package Hkan\Approval\Traits
 */
trait ApprovalTrait {

	/**
	 * Boot the approval trait for a model.
	 *
	 * @return void
	 */
	public static function bootApprovalTrait()
	{
		static::addGlobalScope(new ApprovalScope);
	}

	/**
	 * @return mixed
	 */
	public static function withUnapproved()
	{
		return with(new static)->newQueryWithoutScope(new ApprovalScope);
	}

	/**
	 * @return mixed
	 */
	public static function onlyUnapproved()
	{
		$instance = new static;

		return $instance->newQueryWithoutScope(new ApprovalScope)->where($instance->getApprovalColumn(), false);
	}

	/**
	 * Approve comment
	 */
	public function approve()
	{
		$this->{$this->getApprovalColumn()} = true;
	}

	/**
	 * Unapprove comment
	 */
	public function unapprove()
	{
		$this->{$this->getApprovalColumn()} = false;
	}

	/**
	 * @return mixed
	 */
	public function getApprovalColumn()
	{
		return app('config')->get('approval::config.approval_column');
	}

}
