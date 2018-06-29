<?php

namespace App\Http\Controllers\Api\v1;

use App\Models\ProjectsUsers;
use Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Validator;

/**
 * Class ProjectsUsersController
 *
 * @package App\Http\Controllers\Api\v1
 */
class ProjectsUsersController extends ItemController
{
    /**
     * @return string
     */
    public function getItemClass(): string
    {
        return ProjectsUsers::class;
    }

    /**
     * @return array
     */
    public function getValidationRules(): array
    {
        return [
            'project_id' => 'required|exists:projects,id',
            'user_id'    => 'required|exists:users,id',
        ];
    }

    /**
     * @return string
     */
    public function getEventUniqueNamePart(): string
    {
        return 'projects-users';
    }

    /**
     * @api {any} /api/v1/projects-users/list List
     * @apiDescription Get list of Projects Users relations
     * @apiVersion 0.1.0
     * @apiName GetProjectUsersList
     * @apiGroup ProjectUsers
     *
     * @apiParam {Integer} [project_id] `QueryParam` Project ID
     * @apiParam {Integer} [user_id]    `QueryParam` User ID
     *
     * @apiSuccess {ProjectUsers[]} ProjectUsersList array of Project Users objects
     *
     * @param Request $request
     *
     * @return JsonResponse
     */

    /**
     * @api {post} /api/v1/projects-users/create Create
     * @apiDescription Create Project Users relation
     * @apiVersion 0.1.0
     * @apiName CreateProjectUsers
     * @apiGroup ProjectUsers
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request): JsonResponse
    {
        $requestData = Filter::process($this->getEventUniqueName('request.item.create'), $request->all());

        $validator = Validator::make(
            $requestData,
            Filter::process($this->getEventUniqueName('validation.item.create'), $this->getValidationRules())
        );

        if ($validator->fails()) {
            return response()->json(
                Filter::process($this->getEventUniqueName('answer.error.item.create'), [
                    'error' => 'Validation fail',
                    'reason' => $validator->errors()
                ]),
                400
            );
        }

        $cls = $this->getItemClass();

        $item = Filter::process(
            $this->getEventUniqueName('item.create'),
            $cls::firstOrCreate($this->filterRequestData($requestData))
        );

        return response()->json(
            Filter::process($this->getEventUniqueName('answer.success.item.create'), [
                $item,
            ])
        );
    }

    /**
     * @api {post} /api/v1/projects-users/bulk-create BulkCreate
     * @apiDescription Multiple Create Project Users relation
     * @apiVersion 0.1.0
     * @apiName BulkCreateProjectUsers
     * @apiGroup ProjectUsers
     *
     * @apiParam {Relations[]} array                   Array of object Project User relation
     * @apiParam {Object}      array.object            Object Project User relation
     * @apiParam {Integer}     array.object.project_id Project ID
     * @apiParam {Integer}     array.object.user_id    User ID
     *
     * @apiSuccess {Messages[]} array  Array of Project Users objects
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function bulkCreate(Request $request): JsonResponse
    {
        $requestData = Filter::process($this->getEventUniqueName('request.item.create'), $request->all());
        $result = [];

        if (empty($requestData['relations'])) {
            return response()->json(Filter::process(
                $this->getEventUniqueName('answer.error.item.bulkEdit'), [
                'error' => 'validation fail',
                'reason' => 'relations is empty'
            ]),
                400
            );
        }

        foreach ($requestData['relations'] as $relation) {
            $validator = Validator::make(
                $relation,
                Filter::process($this->getEventUniqueName('validation.item.create'), $this->getValidationRules())
            );

            if ($validator->fails()) {
                $result[] = Filter::process($this->getEventUniqueName('answer.error.item.create'), [
                    'error' => 'Validation fail',
                    'reason' => $validator->errors(),
                    'code' => 400
                ]);
                continue;
            }

            $cls = $this->getItemClass();

            $item = Filter::process(
                $this->getEventUniqueName('item.create'),
                $cls::firstOrCreate($this->filterRequestData($relation))
            );

            $result[] = $item;
        }

        return response()->json(
            Filter::process($this->getEventUniqueName('answer.success.item.create'), [
                'messages' => $result,
            ])
        );
    }

    /**
     * @api {post} /api/v1/projects-users/destroy Destroy
     * @apiDescription Destroy Project Users relation
     * @apiVersion 0.1.0
     * @apiName DestroyProjectUsers
     * @apiGroup ProjectUsers
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function destroy(Request $request): JsonResponse
    {
        $requestData = Filter::process($this->getEventUniqueName('request.item.destroy'), $request->all());

        $validator = Validator::make(
            $requestData,
            Filter::process(
                $this->getEventUniqueName('validation.item.edit'),
                $this->getValidationRules()
            )
        );

        if ($validator->fails()) {
            return response()->json(
                Filter::process($this->getEventUniqueName('answer.error.item.edit'), [
                    'error' => 'Validation fail',
                    'reason' => $validator->errors()
                ]),
                400
            );
        }

        /** @var Builder $itemsQuery */
        $itemsQuery = Filter::process(
            $this->getEventUniqueName('answer.success.item.query.prepare'),
            $this->applyQueryFilter(
                $this->getQuery(), $requestData
            )
        );

        /** @var \Illuminate\Database\Eloquent\Model $item */
        $item = $itemsQuery->first();
        if ($item) {
            $item->delete();
        } else {
            return response()->json(
                Filter::process($this->getEventUniqueName('answer.success.item.remove'), [
                    'error' => 'Item has not been removed',
                    'reason' => 'Item not found'
                ])
            );
        }

        return response()->json(
            Filter::process($this->getEventUniqueName('answer.success.item.remove'), [
                'message' => 'Item has been removed'
            ])
        );
    }

    /**
     * @api {post} /api/v1/projects-users/bulk-destroy BulkDestroy
     * @apiDescription Multiple Destroy Project Users relation
     * @apiVersion 0.1.0
     * @apiName BulkDestroyProjectUsers
     * @apiGroup ProjectUsers
     *
     * @apiParam {Relations[]} array                   Array of object Project User relation
     * @apiParam {Object}      array.object            Object Project User relation
     * @apiParam {Integer}     array.object.project_id Project ID
     * @apiParam {Integer}     array.object.user_id    User ID
     *
     * @apiSuccess {Messages[]} array         Array of Messages object
     * @apiSuccess {Message}    array.object  Message
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function bulkDestroy(Request $request): JsonResponse
    {
        $requestData = Filter::process($this->getEventUniqueName('request.item.destroy'), $request->all());
        $result = [];

        if (empty($requestData['relations'])) {
            return response()->json(Filter::process(
                $this->getEventUniqueName('answer.error.item.bulkEdit'), [
                'error' => 'validation fail',
                'reason' => 'relations is empty'
            ]),
                400
            );
        }

        foreach ($requestData['relations'] as $relation) {
            /** @var Builder $itemsQuery */
            $itemsQuery = Filter::process(
                $this->getEventUniqueName('answer.success.item.query.prepare'),
                $this->applyQueryFilter(
                    $this->getQuery(), $relation
                )
            );

            $validator = Validator::make(
                $relation,
                Filter::process(
                    $this->getEventUniqueName('validation.item.edit'),
                    $this->getValidationRules()
                )
            );

            if ($validator->fails()) {
                $result[] = [
                        'error' => 'Validation fail',
                        'reason' => $validator->errors(),
                        'code' =>400
                ];
                continue;
            }

            /** @var \Illuminate\Database\Eloquent\Model $item */
            $item = $itemsQuery->first();
            if ($item && $item->delete()) {
                $result[] = ['message' => 'Item has been removed'];
            } else {
                $result[] = [
                    'error' => 'Item has not been removed',
                    'reason' => 'Item not found'
                ];
             }
        }

        return response()->json(
            Filter::process($this->getEventUniqueName('answer.success.item.remove'), [
                'messages' => $result
            ])
        );
    }
}
