<?php

use Mockery as m;

class ScopeTest extends PHPUnit_Framework_TestCase {

	public function tearDown()
	{
		m::close();
	}

	public function testApplyingScopeToABuilder()
	{
		$scope = m::mock('Hkan\Approval\Scopes\ApprovalScope[extend]');
		$builder = m::mock('Illuminate\Database\Eloquent\Builder');
		$builder->shouldReceive('getModel')->once()->andReturn($model = m::mock('StdClass'));
		$model->shouldReceive('getApprovalColumn')->once()->andReturn('is_approved');
		$builder->shouldReceive('where')->once()->with('is_approved', true);
		$scope->shouldReceive('extend')->once();
		$scope->apply($builder);
	}

	public function testScopeCanRemoveIsApprovedConstraints()
	{
		$scope = new Hkan\Approval\Scopes\ApprovalScope;
		$builder = m::mock('Illuminate\Database\Eloquent\Builder');
		$builder->shouldReceive('getModel')->andReturn($model = m::mock('StdClass'));
		$model->shouldReceive('getApprovalColumn')->once()->andReturn('is_approved');
		$builder->shouldReceive('getQuery')->andReturn($query = m::mock('StdClass'));
		$query->shouldReceive('getRawBindings')->andReturn([ 'where' => [ 'is_approved' => true ] ]);
		$query->shouldReceive('setBindings');
		$query->wheres = [ [ 'type' => 'Null', 'column' => 'foo' ], [ 'type' => 'Basic', 'column' => 'is_approved' ] ];
		$scope->remove($builder);
		$this->assertEquals($query->wheres, [ [ 'type' => 'Null', 'column' => 'foo' ] ]);
	}

	public function testWithUnapprovedExtension()
	{
		$builder = m::mock('Illuminate\Database\Eloquent\Builder');
		$builder->shouldDeferMissing();
		$scope = m::mock('Hkan\Approval\Scopes\ApprovalScope[remove]');
		$scope->extend($builder);
		$callback = $builder->getMacro('withUnapproved');
		$givenBuilder = m::mock('Illuminate\Database\Eloquent\Builder');
		$scope->shouldReceive('remove')->once()->with($givenBuilder);
		$result = $callback($givenBuilder);
		$this->assertEquals($givenBuilder, $result);
	}

	public function testOnlyUnapprovedExtension()
	{
		$builder = m::mock('Illuminate\Database\Eloquent\Builder');
		$builder->shouldDeferMissing();
		$scope = m::mock('Hkan\Approval\Scopes\ApprovalScope[remove]');
		$scope->extend($builder);
		$callback = $builder->getMacro('onlyUnapproved');
		$givenBuilder = m::mock('Illuminate\Database\Eloquent\Builder');
		$scope->shouldReceive('remove')->once()->with($givenBuilder);
		$givenBuilder->shouldReceive('getQuery')->andReturn($query = m::mock('StdClass'));
		$givenBuilder->shouldReceive('getModel')->andReturn($model = m::mock('StdClass'));
		$model->shouldReceive('getApprovalColumn')->andReturn('is_approved');
		$query->shouldReceive('where')->once()->with('is_approved', false);
		$result = $callback($givenBuilder);
		$this->assertEquals($givenBuilder, $result);
	}
}
