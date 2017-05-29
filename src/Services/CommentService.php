<?php

namespace AoComments\Services;

use AoComments\Models\Comment;
use AoScrud\Core\ScrudService;

class CommentService extends ScrudService
{

    //------------------------------------------------------------------------------------------------------------------
    // DYNAMIC
    //------------------------------------------------------------------------------------------------------------------

    protected $dynamicClass;

    protected $dynamicTable;

    protected $dynamicForeign;

    public function setDynamicClass($dynamicClass)
    {
        $parts = explode('.', app()->make($dynamicClass)->comments()->getQualifiedForeignKeyName());

        $this->dynamicClass = $dynamicClass;
        $this->dynamicTable = $parts[0];
        $this->dynamicForeign = $parts[1];

        return $this;
    }

    protected function applyDynamicFilter($config)
    {
        $model = $config->model();
        $model->dynamicClass = $this->dynamicClass;
        $model->dynamicTable = $this->dynamicTable;
        $model->dynamicForeign = $this->dynamicForeign;

        $id = $config->data()->get($this->dynamicForeign);

        if (!app()->make($this->dynamicClass)->find($id))
            abort(404);

        $config->model($model->whereHas('dynamicWith', function ($query) use ($id) {
            $query->where('id', $id);
        }));
    }

    //------------------------------------------------------------------------------------------------------------------
    // OWNER
    //------------------------------------------------------------------------------------------------------------------

    private $owner;

    protected function setOwner($config)
    {
        $this->owner = app()->make($this->dynamicClass)->find($config->data()->get($this->dynamicForeign));
        if (!$this->owner)
            abort(404);
    }

    //------------------------------------------------------------------------------------------------------------------
    // CONSTRUCTOR
    //------------------------------------------------------------------------------------------------------------------

    private $temp = false;

    public function __construct()
    {
        parent::__construct();


        // SEARCH //----------------------------------------------------------------------------------------------------

        $this->search
            ->model(Comment::class)
            ->columns(['id', 'message'])
            ->otherColumns(['user_id', 'created_at', 'updated_at'])
            ->setAllOrders()
            ->with([
                'user' => [
                    'columns' => ['id', 'name'],
                    'otherColumns' => ['created_at', 'updated_at']
                ]
            ])
            ->rules([
                'id' => '=',
                'user_id' => '=',
                [
                    'message' => '%like%|get:search',
                ]
            ])
            ->onPrepare(function ($config) {
                $this->applyDynamicFilter($config);
            });

        // READ //------------------------------------------------------------------------------------------------------

        $this->read
            ->model(Comment::class)
            ->columns($this->search->columns()->all())
            ->with($this->search->with()->all())
            ->otherColumns($this->search->otherColumns()->all())
            ->onPrepare(function ($config) {
                $this->applyDynamicFilter($config);
            });

        // CREATE //----------------------------------------------------------------------------------------------------

        $this->create
            ->model(Comment::class)
            ->columns(['user_id', 'message'])
            ->rules([
                'message' => 'required',
                //'user_id' => 'required|integer|exists:' . config('ao.tables.users') . ',id'

            ])->onPrepare(function ($config) {
                //$this->temp = $config->data()->get('user_id', false);
                //$config->data()->put('user_id', Auth()->id());

            })->onPrepareEnd(function ($config) {
                $this->setOwner($config);

                $this->temp = $config->data()->get('user_id', false);
                $config->data()->put('user_id', Auth()->id());

            })->onExecuteEnd(function ($config, $result) {
                if ($this->temp)
                    $config->data()->put('user_id', $this->temp);

                $this->owner->comments()->attach($result->id);
            });

        // UPDATE //----------------------------------------------------------------------------------------------------

        $this->update
            ->model(Comment::class)
            ->columns(['message'])
            ->rules([
                'message' => 'required',
                'user_id' => 'sometimes|nullable|integer|exists:' . config('ao.tables.users') . ',id'
            ])
            ->onPrepare(function ($config) {
                $this->applyDynamicFilter($config);

            })->onPrepareEnd(function ($config) {
                if ($config->obj()->user_id != Auth()->id())
                    abort(412, 'Você não pode editar esse comentário');
            });

        // DESTROY //---------------------------------------------------------------------------------------------------

        $this->destroy
            ->model(Comment::class)
            ->onPrepare(function ($config) {
                $this->applyDynamicFilter($config);

            })->onPrepareEnd(function ($config) {
                if ($config->obj()->user_id != Auth()->id())
                    abort(412, 'Você não pode excluir esse comentário');

            })->onExecute(function ($config) {
                $this->setOwner($config);
                $this->owner->comments()->detach($config->data()->get('id'));
            });

        // RESTORE //---------------------------------------------------------------------------------------------------

        $this->restore
            ->model(Comment::class)
            ->onPrepare(function ($config) {
                $this->applyDynamicFilter($config);
            });
    }

}