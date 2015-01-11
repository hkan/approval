<?php

use Mockery as m;

class TraitTest extends PHPUnit_Framework_TestCase {

	public function tearDown()
	{
		m::close();
	}

	public function testApproveChangesIsApprovedColumn()
	{
		$model = m::mock('ModelStub');
		$model->shouldDeferMissing();
		$model->shouldReceive('getApprovalColumn')->andReturn('is_approved');

		$model->approve();

		$this->assertEquals($model->is_approved, true);
	}

	public function testUnapproveChangesIsApprovedColumn()
	{
		$model = m::mock('ModelStub');
		$model->shouldDeferMissing();
		$model->setAttribute('is_approved', true);
		$model->shouldReceive('getApprovalColumn')->andReturn('is_approved');

		$model->unapprove();

		$this->assertEquals($model->is_approved, false);
	}

}

class ModelStub extends Illuminate\Database\Eloquent\Model {

	use Hkan\Approval\Traits\ApprovalTrait;

	protected $attributes = [
		'is_approved' => false
	];

}
