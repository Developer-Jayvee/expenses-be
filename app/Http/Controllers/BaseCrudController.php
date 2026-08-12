<?php

namespace App\Http\Controllers;

use App\Traits\ErrorMessageTrait;
use App\Traits\SuccessMessageTrait;
use App\Traits\UtilitiesTrait;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Queue\InvalidPayloadException;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Throwable;

abstract class BaseCrudController
{
    use ErrorMessageTrait, SuccessMessageTrait , UtilitiesTrait;

    protected Model $baseModel;

    protected mixed $service;
    protected string $storeRequest;
    protected string $updateRequest;

    protected ?string $okMessage = "oK";
    protected ?string $badMessage = "error";


    protected ?int $baseModelId;
    /**
     * @var class-string<\Illuminate\Http\Resources\Json\JsonResource>|null
     */
    protected ?string $resource = null;

    abstract public function setupParams();

    public function __construct()
    {
        $this->setupParams();
    }
    private function getStoreRequest() : FormRequest
    {
        /** @var FormRequest */
        return app($this->storeRequest);
    }
  
    protected function checkModelInstance(): bool 
    {
        if (!$this->baseModel instanceof Model) {
            throw new \Exception("Model Instance not found.", 404);
        }
        return true;
    }
    protected static function checkBaseId(): bool | Throwable
    {
        if(!self::$baseModelId){
            throw new BadRequestHttpException("Missing instance id.");
        }
        return true;
    }
    private function getQueryInstance()
    {
        self::checkModelInstance();
        return $this->baseModel->query();
    }

    protected function getQueryFind()
    {
        self::checkModelInstance();
        return $this->getQueryInstance()->findOrFail($this->baseModelId);
    }
    public function index(): JsonResponse|JsonResource
    {
        try {
            self::checkModelInstance();
            $data =  $this->baseModel->query()->get();
            if($this->resource) {
                return $this->successMessage("Data fetched",
                    $this->resource::collection($data)
                );
            }
            return $this->successMessage("Data feched", $data->toArray());
        } catch (\Throwable $th) {
            return $this->errorResponse($th);
        }
    }
    public function storeQuery() : JsonResponse
    {
        try {
            self::checkModelInstance();
            
            $input = self::getStoreRequest()->validated();

            if (!$input) {
                throw new InvalidPayloadException("Missing payload", 422);
            }
            $model = new $this->baseModel();
            $model->create($input);

            return $this->successMessage($this->okMessage, $this->baseModel->fresh());
        } catch (\Throwable $th) {
            return $this->errorResponse($th, 500);
        }
    }
    public function showQuery() : JsonResponse
    {
        try {
            if(!$this->baseModelId){
                throw new BadRequestHttpException("Missing instance id.");
            }
            self::checkModelInstance();
            $data = $this->baseModel->query()->findOrFail($this->baseModelId); 

            return $this->successMessage($this->okMessage,$data);
        } catch (\Throwable $th) {
            return $this->errorResponse($th,$th->getCode());
        }
    }
    public function updateQuery(FormRequest $request , int $id) : JsonResponse 
    {
        try {
            self::checkModelInstance();

            $data = $request->validated();
            $this->baseModelId = $id;

            $query = $this->getQueryFind();

            if($query) {
                $query->update($data);
            }
            return $this->successMessage($this->okMessage,[
                'response' => $query
            ]);
        } catch (\Throwable $th) {
            return $this->errorResponse($th,$th->getCode());
        }
    }
    public function updateQueryByData(array $data , int $id): JsonResponse 
    {
        try {
            self::checkModelInstance();

            $this->baseModelId = $id;

            $query = $this->getQueryFind();
            if($query) {
                $query->update($data);
            }
            return $this->successMessage($this->okMessage , [
                'response' => $query
            ]);            
        } catch (\Exception $e) {
            return $this->errorResponse($e,$e->getCode());
        }
    }
    public function destroyQuery() : JsonResponse 
    {
        try {
            $this->getQueryFind()->delete();

            $response = $this->setReturnResponse([],$this->okMessage);
            return $this->successMessage($this->okMessage,$response);
        } catch (\Throwable $th) {
            return $this->errorResponse($th,$th->getCode());
        }
    }
}
