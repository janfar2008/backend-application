<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Requests\v1\Invitation\CreateInvitationRequest;
use App\Http\Requests\v1\Invitation\UpdateInvitationRequest;
use App\Models\Invitation;
use App\Services\InvitationService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InvitationController extends ItemController
{
    /**
     * @var InvitationService
     */
    protected InvitationService $service;

    /**
     * InvitationController constructor.
     * @param InvitationService $service
     */
    public function __construct(InvitationService $service)
    {
        parent::__construct();

        $this->service = $service;
    }

    /**
     * Get the controller rules.
     *
     * @return array
     */
    public static function getControllerRules(): array
    {
        return [
            'index' => 'invitation.list',
            'count' => 'invitation.list',
            'create' => 'invitation.create',
            'resend' => 'invitation.resend',
            'show' => 'invitation.show',
            'destroy' => 'invitation.remove',
        ];
    }

    /**
     * Get the validation rules.
     *
     * @return array
     */
    public function getValidationRules(): array
    {
        return [];
    }

    /**
     * Get the event unique name part.
     *
     * @return string
     */
    public function getEventUniqueNamePart(): string
    {
        return 'invitation';
    }

    /**
     * Get the model class.
     *
     * @return string
     */
    public function getItemClass(): string
    {
        return Invitation::class;
    }

    /**
     * @api             {post} /v1/invitations/show Show
     * @apiDescription  Show invitation.
     *
     * @apiVersion      1.0.0
     * @apiName         Show Invitation
     * @apiGroup        Invitation
     *
     * @apiUse          AuthHeader
     *
     * @apiParam {Integer} id  Invitation ID
     *
     * @apiParamExample {json} Request Example
     * {
     *   "id": 1
     * }
     *
     * @apiSuccess {Boolean}  success  Indicates successful request when `TRUE`
     * @apiSuccess {Array}    res      Array of records containing the id, email, key, expiration date and role id
     *
     * @apiUse InvitationObject
     *
     * @apiSuccessExample {json} Response Example
     *  HTTP/1.1 200 OK
     *  {
     *    "success": true,
     *    "res": [
     *      {
     *          "id": 1
     *          "email": "test@example.com",
     *          "key": "06d4a090-9675-11ea-bf39-5f84c549e29c",
     *          "expires_at": "2020-01-01T00:00:00.000000Z",
     *          "role_id": 1
     *      }
     *  }
     *
     * @apiUse          400Error
     * @apiUse          UnauthorizedError
     *
     */

    /**
     * @api             {get} /v1/invitations/list List
     * @apiDescription  Get list of invitations.
     *
     * @apiVersion      1.0.0
     * @apiName         Invitation List
     * @apiGroup        Invitation
     *
     * @apiUse          AuthHeader
     *
     * @apiSuccess {Boolean}  success  Indicates successful request when `TRUE`
     * @apiSuccess {Array}   res      Array of records containing the id, email, key, expiration date and role id
     *
     * @apiUse InvitationObject
     *
     * @apiSuccessExample {json} Response Example
     *  HTTP/1.1 200 OK
     *  {
     *    "success": true,
     *    "res": [
     *      {
     *          "id": 1
     *          "email": "test@example.com",
     *          "key": "06d4a090-9675-11ea-bf39-5f84c549e29c",
     *          "expires_at": "2020-01-01T00:00:00.000000Z",
     *          "role_id": 1
     *      }
     *  }
     *
     * @apiUse          400Error
     * @apiUse          UnauthorizedError
     *
     */

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     *
     * @api             {post} /v1/invitations/create Create
     * @apiDescription  Creates a unique invitation token and sends an email to the users
     *
     * @apiVersion      1.0.0
     * @apiName         Create Invitation
     * @apiGroup        Invitation
     *
     * @apiUse          AuthHeader
     *
     * @apiParam {Array}    users          List of users to send an invitation to
     * @apiParam {String}   users.email    User email
     * @apiParam {Integer}  users.role_id  ID of the role that will be assigned to the created user
     *
     * @apiParamExample {json} Request Example
     *  {
     *    "users": [
     *      {
     *        email: "test@example.com",
     *        role_id: 1
     *      }
     *    ]
     *  }
     *
     * @apiSuccess {Boolean}  success  Indicates successful request when `TRUE`
     * @apiSuccess {String}   res      Array of records containing the id, email, key, expiration date and role id
     *
     * @apiUse InvitationObject
     *
     * @apiSuccessExample {json} Response Example
     *  HTTP/1.1 200 OK
     *  {
     *    "success": true,
     *    "res": [
     *      {
     *          "id": 1
     *          "email": "test@example.com",
     *          "key": "06d4a090-9675-11ea-bf39-5f84c549e29c",
     *          "expires_at": "2020-01-01T00:00:00.000000Z",
     *          "role_id": 1
     *      }
     *    ]
     *  }
     *
     * @apiErrorExample {json} Email is not specified
     *  HTTP/1.1 400 Bad Request
     *  {
     *     "success": false,
     *     "error_type": "validation",
     *     "message": "Validation error",
     *     "info": {
     *          "users.0.email": [
     *              "The email field is required."
     *         ]
     *     }
     * }
     *
     * @apiErrorExample {json} Email already exists
     *  HTTP/1.1 400 Bad Request
     *  {
     *      "success": false,
     *      "error_type": "validation",
     *      "message": "Validation error",
     *      "info": {
     *          "users.0.email": [
     *              "The email test@example.com has already been taken."
     *          ]
     *      }
     *  }
     *
     * @apiUse          400Error
     * @apiUse          UnauthorizedError
     *
     */
    public function create(Request $request): JsonResponse
    {
        $requestData = app(CreateInvitationRequest::class)->validated();

        $invitations = [];

        foreach ($requestData['users'] as $user) {
            $invitations[] = $this->service->create($user);
        }

        return new JsonResponse([
            'success' => true,
            'res' => $invitations,
        ]);
    }

    /**
     * @param UpdateInvitationRequest $request
     * @return JsonResponse
     * @throws Exception
     *
     * @api             {post} /v1/invitations/resend Resend
     * @apiDescription  Updates the token expiration date and sends an email to the user's email address.
     *
     * @apiVersion      1.0.0
     * @apiName         Update Invitation
     * @apiGroup        Invitation
     *
     * @apiUse          AuthHeader
     *
     * @apiParam {Integer}  id  Invitation ID
     *
     * @apiParamExample {json} Request Example
     *  {
     *    "id": 1
     *  }
     *
     * @apiSuccess {Boolean}  success  Indicates successful request when `TRUE`
     * @apiSuccess {Array}    res      Invitation data
     *
     * @apiUse InvitationObject
     *
     * @apiSuccessExample {json} Response Example
     *  HTTP/1.1 200 OK
     *  {
     *    "success": true,
     *    "res": {
     *      "id": 1
     *      "email": "test@example.com",
     *      "key": "06d4a090-9675-11ea-bf39-5f84c549e29c",
     *      "expires_at": "2020-01-01T00:00:00.000000Z",
     *      "role_id": 1
     *    }
     *  }
     *
     * @apiErrorExample {json} The id does not exist
     *  HTTP/1.1 400 Bad Request
     *  {
     *      "success": false,
     *      "error_type": "validation",
     *      "message": "Validation error",
     *      "info": {
     *          "id": [
     *              "The selected id is invalid."
     *          ]
     *      }
     * }
     *
     * @apiUse          400Error
     * @apiUse          UnauthorizedError
     *
     */
    public function resend(UpdateInvitationRequest $request): JsonResponse
    {
        $requestData = $request->validated();

        $invitation = $this->service->update($requestData['id']);

        return new JsonResponse([
            'success' => true,
            'res' => $invitation,
        ]);
    }

    /**
     * @api             {post} /v1/invitations/remove Destroy
     * @apiDescription  Destroy User
     *
     * @apiVersion      1.0.0
     * @apiName         Destroy Invitation
     * @apiGroup        Invitation
     *
     * @apiUse          AuthHeader
     *
     * @apiParam {Integer}  id  ID of the target invitation
     *
     * @apiParamExample {json} Request Example
     * {
     *   "id": 1
     * }
     *
     * @apiSuccess {Boolean}  success  Indicates successful request when `TRUE`
     * @apiSuccess {String}   message  Destroy status
     *
     * @apiSuccessExample {json} Response Example
     *  HTTP/1.1 200 OK
     *  {
     *    "success": true,
     *    "message": "Item has been removed"
     *  }
     *
     * @apiUse          400Error
     * @apiUse          ValidationError
     * @apiUse          ForbiddenError
     * @apiUse          UnauthorizedError
     */
}
