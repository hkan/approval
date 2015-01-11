# Approval
![Build status](https://travis-ci.org/hkan/approval.svg?branch=master)
Content approval system for Laravel. Works just like Laravel's own soft deleting system.

## Requirements
PHP >= 5.5

## Installation
**Step 1:** Add the following line to your `composer.json`'s `require` array.

    "hkan/approval": "dev-master"

**Step 2:** Add `is_approved` column to your desired models' tables (Recommended migration line)

    $table->boolean('is_approved')->default(false);

**Step 3:** Copy the following line to the `providers` array of `app/config/app.php`

    'Hkan\Approval\ApprovalServiceProvider'

**Step 4:** Add the trait to your model(s).

    use \Hkan\Approval\Traits\ApprovalTrait;

## Usage

#### Approve and unapprove posts

    $post->approve()
    $post->unapprove()

#### Approved and unapproved posts

    Post::all() // Only approved posts
    Post::onlyUnapproved()->get() // Only unapproved posts
    Post::withUnapproved()->get() // Both approved and unapproved posts

## Contribution
Issues, pull requests and feature requests are welcome.