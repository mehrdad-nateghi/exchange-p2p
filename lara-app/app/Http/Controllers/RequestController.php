<?php

namespace App\Http\Controllers;

use App\Http\Resources\RequestResource;
use App\Models\PaymentMethod;
use App\Models\Request as RequestModel;
use App\Models\User as UserModel;
use App\Rules\FeasibilityThresholdRange;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\FinancialController;
use App\Http\Resources\PaymentMethodResource;
use App\Models\Country;

/**
 * @OA\Tag(
 *     name="Requests",
 *     description="APIs for managing requests"
 * )
 */
class RequestController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/requests",
     *     summary="Get all sell requests",
     *     tags={"Requests"},
     *      @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="Filter requests by type (sell or buy)",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             enum={"sell", "buy"}
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        if ($request->has('type')){
            $type = $request->input('type') == 'sell'? \App\Enums\RequestTypeEnum::Sell : \App\Enums\RequestTypeEnum::Buy;
            $requests = RequestModel::where('type' , $type)->get();
        }
        else{
            $requests = RequestModel::all();
        }

        return response()->json(RequestResource::collection($requests), 200);
    }

    /**
     * @OA\Get(
     *     path="/api/requests/applicant/{applicantId}",
     *     summary="Get all requests of the specific applicant",
     *     tags={"Requests"},
     *     @OA\Parameter(
     *         name="applicantId",
     *         in="path",
     *         description="ID of the applicant to fetch its requests",
     *         required=true,
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *      ),
     *     @OA\Response(
     *         response=404,
     *         description="Applicant not found"
     *     )
     *     )
     * )
     */
    public function getApplicantAllRequests($applicantId)
    {
        $user = UserModel::find($applicantId);

        $response = '';

        if($user instanceof UserModel && $user->type == \App\Enums\UserTypeEnum::Applicant) {
            $requests = $user->requests()->get();
            $response = response()->json(['requests' => RequestResource::collection($requests)], 200);
        }
        else {
            $response = response()->json(['message' => 'Applicant not found.'], 404);
        }

        return $response;
    }

    /**
     * @OA\Get(
     *     path="/api/requests/applicant/{applicantId}/{requestId}",
     *     summary="Get specific request of the specific applicant",
     *     tags={"Requests"},
     *     @OA\Parameter(
     *         name="applicantId",
     *         in="path",
     *         description="ID of the applicant to fetch its request",
     *         required=true,
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Parameter(
     *         name="requestId",
     *         in="path",
     *         description="ID of the request",
     *         required=true,
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *      ),
     *     @OA\Response(
     *         response=404,
     *         description="Applicant not found or Request not found"
     *     )
     *     )
     * )
     */
    public function getApplicantRequest($applicantId, $requestId)
    {
        $response = '';

        $user = UserModel::find($applicantId);
        if($user instanceof UserModel && $user->type == \App\Enums\UserTypeEnum::Applicant ) {
            $request = $user->requests()->where('id',$requestId)->first();

            if($request instanceof RequestModel) {
                $response = response()->json(['request' => new RequestResource($request)], 200);
            }
            else {
                $response = response()->json(['message' => 'Request not found.'], 404);
            }
        }
        else {
            $response = response()->json(['message' => 'Applicant not found.'], 404);
        }

        return $response;
    }


    /**
     * @OA\Get(
     *     path="/api/requests/filter-requests",
     *     summary="Get all requests by filter",
     *     tags={"Requests"},
     *     @OA\Parameter(
     *         name="payment_methods[]",
     *         in="query",
     *         description="Filter requests by payment methods",
     *         @OA\Schema(
     *             type="array",
     *             @OA\Items(type="integer")
     *         ),
     *         style="form"
     *     ),
     *     @OA\Parameter(
     *         name="request_status",
     *         in="query",
     *         description="Filter requests by status",
     *         @OA\Schema(type="string"),
     *         style="form"
     *     ),
     *     @OA\Parameter(
     *         name="trade_volume_min",
     *         in="query",
     *         description="Filter requests by minimum trade volume",
     *         @OA\Schema(type="number"),
     *         style="form"
     *     ),
     *     @OA\Parameter(
     *         name="trade_volume_max",
     *         in="query",
     *         description="Filter requests by maximum trade volume",
     *         @OA\Schema(type="number"),
     *         style="form"
     *     ),
     *     @OA\Parameter(
     *         name="order",
     *         in="query",
     *         description="Sort requests by order",
     *         @OA\Schema(type="string", enum={"asc", "desc"}),
     *         style="form"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     )
     * )
     */
    public function getAllRequestsByFilter(Request $request)
    {
        // Log::debug($request->all());

        $query = RequestModel::with('paymentMethods');

        // Filter requests by PaymentMethods
        if ($request->has('payment_methods')) {
            $paymentMethods = $request->input('payment_methods');
            $query->whereHas('paymentMethods', function ($q) use ($paymentMethods) {
                $q->whereIn('payment_methods.id', $paymentMethods);
            });
        }

        // Filter requests by status
        if ($request->has('request_status')) {
            $status = $request->input('request_status');
            $query->where('status', $status);
        }

        // Filter requests by trade_volume
        if ($request->has('trade_volume_min')) {
            $min = $request->input('trade_volume_min');
            $query->where('trade_volume', '>=', $min);
        }

        if ($request->has('trade_volume_max')) {
            $max = $request->input('trade_volume_max');
            $query->where('trade_volume', '<=', $max);
        }

        // Sort requests by order
        if ($request->has('order')) {
            $order = $request->input('order');
            $query->orderBy('created_at', $order);
        }

        $requests = $query->get();

        return response()->json(['requests' =>  RequestResource::collection($requests)], 200);
    }

    // Validate input fields through the request creation process
    public function createRequestValidation(Request $request){
        return $this->validate($request, [
            'type' => 'required|in:0,1',
            'trade_volume' => 'required|numeric',
            'lower_bound_feasibility_threshold' => 'required|numeric',
            'upper_bound_feasibility_threshold' => 'required|numeric',
            'description' => 'string',
            'acceptance_threshold' => ['required', new FeasibilityThresholdRange],
            'request_rate' => ['required', new FeasibilityThresholdRange],
            'request_payment_methods' => 'required|array|min:1',
            'applicant_id' => 'required'
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/requests/create",
     *     summary="Create new request",
     *     tags={"Requests"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="applicant_id", type="number"),
     *  *          @OA\Property(property="type", type="number", enum={0, 1}, description="0: Buy Request, 1: Sell Request"),
     *             @OA\Property(property="trade_volume", type="number"),
     *             @OA\Property(property="lower_bound_feasibility_threshold", type="number"),
     *             @OA\Property(property="upper_bound_feasibility_threshold", type="number"),
     *             @OA\Property(property="acceptance_threshold", type="number"),
     *             @OA\Property(property="request_rate", type="number"),
     *             @OA\Property(property="request_payment_methods", type="array", @OA\Items(type="integer")),
     *             @OA\Property(property="description", type="string"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Request created successfully",
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable request - Invalid input data",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Applicant not found or One or more selected payment methods are not available for this applicant",
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error - An error occurred while creating the request",
     *     )
     * )
     */
    public function create(Request $request){

        // Validate inputs
        try {
            $validated_data = $this->createRequestValidation($request);
        }
        catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422); // 422 Unprocessable Request
        }

        // Check if the applicant exists
        $applicant = UserModel::find($validated_data['applicant_id']);
        if(!($applicant instanceof UserModel)) {
            return $response = response()->json(['message' => 'Applicant not found.'], 404);
        }

        // Check if the request payment methods exist and associated with applicant
        $applicant_linked_methods = $applicant->linkedMethods;
        $request_payment_methods = $validated_data['request_payment_methods'];
        $applicant_payment_methods = [];
        foreach($applicant_linked_methods as $lm){
            $payment_method = $lm->paymentMethod;
            array_push($applicant_payment_methods, $payment_method->id);
        }
        $difference = array_diff($request_payment_methods, $applicant_payment_methods); // Check all items of request_payment_methods list exist in $applicant_payment_methods
        if(!empty($difference)) {
            return $response = response()->json(['message' => 'One or more selected payment methods are not available for this applicant.'], 404);
        }

        // Create request on database
        $new_request = RequestModel::create([
            'type' => $validated_data['type'],
            'support_id' => 'RE',
            'trade_volume' => $validated_data['trade_volume'],
            'lower_bound_feasibility_threshold' => $validated_data['lower_bound_feasibility_threshold'],
            'upper_bound_feasibility_threshold' => $validated_data['upper_bound_feasibility_threshold'],
            'acceptance_threshold' => $validated_data['acceptance_threshold'],
            'request_rate' => $validated_data['request_rate'],
            'description' => $validated_data['description'],
            'status' => \App\Enums\RequestStatusEnum::Pending ,
            'is_removed' => False,
            'applicant_id' => $applicant->id
        ]);

        if($new_request instanceof RequestModel) {
            // Set the support_id using the pattern 'RE' + id
            $new_request->update(['support_id' => 'RE-' . $new_request->id]);

            // Attach payment methods to the request using the relationship
            $new_request->paymentMethods()->attach($request_payment_methods);

            // Refresh the object to ensure attributes like created_at are up-to-date
            $new_request->refresh();

            return response()->json(['message' => 'Request created successfully.', 'request' => new RequestResource($new_request)], 200);
        }
        else {
            return response()->json(['message' => 'An error occurred while creating the request.'], 500);
        }
    }


    /**
     * @OA\Get(
     *     path="/api/requests/setup/{countryId}",
     *     summary="Get setup information for request creation and request update.",
     *     tags={"Requests"},
     *     @OA\Parameter(
     *         name="countryId",
     *         in="path",
     *         description="ID of the country to fetch its paymentMethods",
     *         required=true,
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *      ),
     *     @OA\Response(
     *         response=404,
     *         description="Country/FinancialInformation/EuroDailyRate not found"
     *     )
     *     )
     * )
     */
    public function getRequestInitialInformation($countryId){

        $country = Country::find($countryId);
        if(!($country instanceof Country)) {
            return response()->json(['message' => 'Country not found!'], 404);
        }

        $system_payment_methods = $country->paymentMethods()->get();

        $euro_daily_rate = config('constants.Euro_Daily_Rate');

        // get feasibility range
        $financial_controller = new FinancialController();
        $feasibility_range_response = $financial_controller->getFeasibilityRange();
        if($feasibility_range_response['status'] == 200){
            $result = [
                'payment_methods' => PaymentMethodResource::collection($system_payment_methods),
                'feasibility_range' => $feasibility_range_response['feasibility_range'],
                'euoro_daily_rate' => $euro_daily_rate
            ];
            return response()->json(['data' => $result], 200);
        } else{
            return response()->json(['message' => $feasibility_range_response['message']], 404);
        }
    }

}
