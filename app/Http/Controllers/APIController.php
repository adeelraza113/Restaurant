<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\TblTableType;
use App\Models\TblSittingTableS;
use App\Models\TblTablePaymentPlan;
use App\Models\ReservationPayment;
use App\Models\TblTableReservation;
use App\Models\TblBrand;
use App\Models\TblCategory;
use App\Models\TblSubCategory;
use App\Models\TblSubSubCategory;
use App\Models\TblProducts;
use App\Models\TblAllPayments;
use App\Models\PurchaseMaster;
use App\Models\Vendor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Exception;
use Carbon\Carbon;

class APIController extends Controller
{
    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|unique:users',
                'password' => 'required|confirmed|min:6',
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 400);
            }
            $user = User::create([
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json([
                'message' => 'User registered successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to register user', 'message' => $e->getMessage()], 500);
        }
    }

    public function login(Request $request)
{
    try {
        // Validate the input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        // Check if the email exists
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                'error' => 'Login failed','message' => 'The email or password is incorrect.',], 400);
        }
        // Check if the password is correct
        if (!Hash::check($request->password, $user->password)) {
            return response()->json([ 'error' => 'Login failed', 'message' => 'The email or password is incorrect.',], 400);
        }
        // Create the token
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json(['message' => 'Login successful','token' => $token,], 200);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Login failed','message' => $e->getMessage(),], 500);
    }
}

    public function updateProfile(Request $request)
{
    try {
        $user = auth()->user();
        $rules = [
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'contact' => 'sometimes|string|max:15',
            'location' => 'sometimes|string|max:255',
            'age' => 'sometimes|integer|min:0',
            'gender' => 'sometimes|in:male,female,other',
            'language' => 'sometimes|array',
            'country_preference' => 'sometimes|array',
            'food_preference' => 'sometimes|array',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }
        $user->first_name = $request->first_name ?? $user->first_name;
        $user->last_name = $request->last_name ?? $user->last_name;
        $user->contact = $request->contact ?? $user->contact;
        $user->location = $request->location ?? $user->location;
        $user->age = $request->age ?? $user->age;
        $user->gender = $request->gender ?? $user->gender;
        $user->language = $request->language ?? $user->language;
        $user->country_preference = $request->country_preference ?? $user->country_preference;
        $user->food_preference = $request->food_preference ?? $user->food_preference;
        $user->save();

        return response()->json(['message' => 'Profile updated successfully'], 200);
    } catch (\Exception $e) {
        // Log the error
        \Log::error('Update Profile Error: ' . $e->getMessage());
        return response()->json(['error' => 'Failed to update profile', 'message' => $e->getMessage()], 500);
    }
}

    public function logout(Request $request)
    {
        try {
            $request->user()->tokens()->delete();

            return response()->json(['message' => 'Logged out successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to log out', 'message' => $e->getMessage()], 500);
        }
    }

    public function generateOtp(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|exists:users,email',
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 400);
            }
            $otp = rand(100000, 999999); // Generate a 6-digit OTP
            $expiresAt = Carbon::now()->addMinutes(10);
            // Store OTP in database (or cache)
            DB::table('password_resets')->updateOrInsert(
                ['email' => $request->email],
                ['otp' => $otp, 'expires_at' => $expiresAt]
            );
            // In production, send the OTP via SMS or Email
            return response()->json(['message' => 'OTP generated successfully', 'otp' => $otp], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to generate OTP', 'message' => $e->getMessage()], 500);
        }
    }

    public function resetPasswordWithOtp(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|exists:users,email',
                'otp' => 'required|numeric',
                'password' => 'required|confirmed|min:6',
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 400);
            }
            // Verify OTP
            $record = DB::table('password_resets')->where('email', $request->email)->first();
            if (!$record || $record->otp != $request->otp || Carbon::now()->isAfter($record->expires_at)) {
                return response()->json(['error' => 'Invalid or expired OTP'], 400);
            }
            // Update password
            $user = User::where('email', $request->email)->first();
            $user->update(['password' => Hash::make($request->password)]);
            // Delete OTP record
            DB::table('password_resets')->where('email', $request->email)->delete();

            return response()->json(['message' => 'Password reset successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to reset password', 'message' => $e->getMessage()], 500);
        }
    }

    public function createTableType(Request $request)
    {
        $request->validate([
            'Table_Type' => 'required|string|max:100',
        ]);
        $user = Auth::user();
         $userEmail = $user->email;

        try {
            $tableType = TblTableType::create([
                'Table_Type' => $request->Table_Type,
                'Added_By' =>  $userEmail,
            ]);
            return response()->json(['message' => 'Table Type added successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error adding Table Type', 'error' => $e->getMessage()], 500);
        }
    }

    public function createSittingTable(Request $request)
    {
        try {
            // Validate input
            $validator = Validator::make($request->all(), [
                'TableName' => 'required|string|max:100',
                'TableNo' => 'required|integer',
                'SittingCapacity' => 'required|integer',
                'SittingPlan' => 'required|integer',
                'TableTypeID' => 'required|integer',
                'ImageName' => 'required|string|max:100', // Image name (string)
                'Image' => 'required|url', // Validate the image URL
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'error' => 'Validation failed',
                    'messages' => $validator->errors(),
                ], 400);
            }
            $imageUrl = $request->Image;
            // Fetch the image from the URL
            $imageContent = file_get_contents($imageUrl);
            if ($imageContent === false) {
                return response()->json([
                    'error' => 'Failed to fetch image from the URL',], 400); }
            // Generate a unique name for the image
            $imageName = time() . '_' . uniqid() . '.png'; // Adjust the extension as needed
            $imagePath = 'images/' . $imageName;
            Storage::disk('public')->put($imagePath, $imageContent);
            $sittingTable = tblSittingTables::create([
                'TableName' => $request->TableName,
                'TableNo' => $request->TableNo,
                'SittingCapacity' => $request->SittingCapacity,
                'SittingPlan' => $request->SittingPlan,
                'TableTypeID' => $request->TableTypeID,
                'Added_By' => Auth::user()->email,
                'ImageName' => $request->ImageName,
                'ImagePath' => 'storage/' . $imagePath, // Save the image path to the database
                'Revision' => 0,
            ]);
            return response()->json([
                'message' => 'Sitting Table created successfully','data' => $sittingTable, ], 200);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'An error occurred while creating the sitting table',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    
    public function createPaymentPlan(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'SittingTableID' => 'required|integer|exists:tblSittingTableS,id',
                'PricePerHour' => 'required|numeric|min:0',
                'PricePerExtraSeat' => 'required|numeric|min:0',
                'Discount' => 'nullable|numeric|min:0|max:100',
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $user = Auth::user();
            $userEmail = $user->email;
            $paymentPlan = TblTablePaymentPlan::create([
                'SittingTableID' => $request->SittingTableID,
                'PricePerHour' => $request->PricePerHour,
                'PricePerExtraSeat'=> $request->PricePerExtraSeat,
                'Discount'=> $request->Discount,
                'Added_By' => $userEmail, 
        ]);
            return response()->json(['message' => 'Payment plan created successfully'], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'An error occurred while creating the payment plan', 'details' => $e->getMessage()], 500);
        }
    }

    public function createReservation(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'UserID' => 'required|integer|exists:users,id',
                'SittingTableID' => 'required|integer|exists:tblSittingTableS,id',
                'SittingPlan' => 'required|integer',
                'ReservationNumber' => 'required|string|max:50|unique:tblTableReservation,ReservationNumber',
                'StartTime' => 'required|date',
                'EndTime' => 'required|date|after:StartTime',
                'ExtendedTime' => 'nullable|date|after:EndTime',
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $user = Auth::user();
            $userEmail = $user->email;
            $reservation = TblTableReservation::create([
               'UserID' => $request->UserID,
                'SittingTableID'=> $request->SittingTableID,
                'SittingPlan'=> $request->SittingPlan,
                'ReservationNumber'=> $request->ReservationNumber,
                'StartTime'=> $request->StartTime,
                'EndTime'=> $request->EndTime,
                'ExtendedTime'=> $request->ExtendedTime,
                'Added_By' => $userEmail, 
            ]);
            return response()->json(['message' => 'Reservation created successfully'], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'An error occurred while creating the reservation', 'details' => $e->getMessage()], 500);
        }
    }
    
    public function updateTableType(Request $request)
    {
        try {
            $id = $request->query('id');
            if (!$id) {
                return response()->json(['error' => 'ID is required in the query parameters'], 400);
            }
            $user = Auth::user();
            $userEmail = $user->email;
            $tableType = TblTableType::findOrFail($id);
            $tableType->update([
                'Table_Type' => $request->input('Table_Type', $tableType->Table_Type),
                'Updated_By' => $userEmail,
                'UpdatedDateTime' => Carbon::now(),
                'Revision' => $tableType->Revision + 1,
            ]);
            return response()->json(['message' => 'Table Type updated successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update Table Type', 'details' => $e->getMessage()], 500);
        }
    }
    
    public function updateSittingTable(Request $request)
    {
        try {
            $user = Auth::user();
            $userEmail = $user->email;
            $id = $request->query('id');
            $sittingTable = TblSittingTableS::findOrFail($id);
            $updateData = [];
            if ($request->has('TableName')) {
                $updateData['TableName'] = $request->input('TableName');
            }
            if ($request->has('TableNo')) {
                $updateData['TableNo'] = $request->input('TableNo');
            }
            if ($request->has('SittingCapacity')) {
                $updateData['SittingCapacity'] = $request->input('SittingCapacity');
            }
            if ($request->has('SittingPlan')) {
                $updateData['SittingPlan'] = $request->input('SittingPlan');
            }
            if ($request->has('TableTypeID')) {
                $updateData['TableTypeID'] = $request->input('TableTypeID');
            }
            if ($request->has('isReserved')) {
                $updateData['isReserved'] = (int)$request->input('isReserved');
            }
            if ($request->has('show')) {
                $updateData['show'] = (int)$request->input('show');
            }
            $updateData['Updated_By'] = $userEmail;
            $updateData['UpdatedDateTime'] = Carbon::now();
            $updateData['Revision'] = $sittingTable->Revision + 1;
            $sittingTable->update($updateData);
            return response()->json(['message' => 'Sitting Table updated successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update Sitting Table', 'details' => $e->getMessage()], 500);
        }
    }
    
 public function updatePaymentPlan(Request $request)
    {
        try {
            $user = Auth::user();
            $userEmail = $user->email;
            $id = $request->query('id');
            if (!$id) {
                return response()->json(['error' => 'Payment Plan ID is required in the query parameters'], 400);}
            $paymentPlan = TblTablePaymentPlan::findOrFail($id);
            $fieldsToUpdate = $request->only([
                'PricePerHour',
                'PricePerExtraSeat',
                'Discount',
            ]);
            $fieldsToUpdate['Updated_By'] = $userEmail;
            $fieldsToUpdate['UpdatedDateTime'] = Carbon::now();
            $fieldsToUpdate['Revision'] = $paymentPlan->Revision + 1;
            $paymentPlan->update($fieldsToUpdate);
            return response()->json(['message' => 'Payment Plan updated successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update Payment Plan', 'details' => $e->getMessage()], 500);
        }
    }
    
    public function updateReservation(Request $request)
    {
        try {
            $reservationId = $request->query('id');
            if (!$reservationId) {
                return response()->json(['error' => 'Reservation ID is required in query parameters'], 400);
            }
            $user = Auth::user();
            $userEmail = $user->email;
            $reservation = TblTableReservation::findOrFail($reservationId);
            $fieldsToUpdate = $request->only([
                'SittingTableID','SittingPlan','ReservationNumber',
                'StartTime','EndTime', 'ExtendedTime',
            ]);
            $fieldsToUpdate['Updated_By'] = $userEmail;
            $fieldsToUpdate['UpdatedDateTime'] = Carbon::now();
            $fieldsToUpdate['Revision'] = $reservation->Revision + 1;
            $reservation->update($fieldsToUpdate);
            return response()->json(['message' => 'Reservation updated successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update Reservation', 'details' => $e->getMessage()], 500);
        }
    }
    
    public function deleteTableType(Request $request)
    {
        try {
            $id = $request->query('id');
            if (!$id) {
                return response()->json(['error' => 'Table Type ID is required'], 400);}
            $tableType = TblTableType::findOrFail($id);
            $tableType->delete();     
            return response()->json(['message' => 'Table Type deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete Table Type', 'details' => $e->getMessage()], 500);
        }
    }
    
    public function deleteSittingTable(Request $request)
    {
        try {
            $id = $request->query('id'); 
            if (!$id) {return response()->json(['error' => 'The id parameter is required'], 400); }
            $sittingTable = TblSittingTableS::findOrFail($id);
            $sittingTable->delete();
            return response()->json(['message' => 'Sitting Table deleted successfully'], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Sitting Table not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete Sitting Table', 'details' => $e->getMessage()], 500);
        }
    }
    
    public function deletePaymentPlan(Request $request)
    {
        try {
            $id = $request->query('id');
            if (!$id) { return response()->json(['error' => 'Payment Plan ID is required'], 400); }
            $paymentPlan = TblTablePaymentPlan::findOrFail($id);
            $paymentPlan->delete();
            return response()->json(['message' => 'Payment Plan deleted successfully'], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Payment Plan not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete Payment Plan', 'details' => $e->getMessage()], 500);
        }
    }
    
    public function deleteReservation(Request $request)
{
    try {
        $id = $request->query('id'); // Get the 'id' from query parameters
        if (!$id) {
            return response()->json(['error' => 'Reservation ID is required'], 400); }
        $reservation = TblTableReservation::findOrFail($id);
        $reservation->delete();
        return response()->json(['message' => 'Reservation deleted successfully'], 200);
    }catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        return response()->json(['error' => 'Reservation Table id not found'], 404);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Failed to delete reservation', 'details' => $e->getMessage()], 500);
    }
}

public function getTablesByReservationStatus(Request $request)
{
    try {
        $status = $request->query('status');
        $data = $request->query('data'); 
        if (!in_array($status, ['all', '1', '0'])) {
            return response()->json(['error' => 'Invalid status parameter. Use "all", "1", or "0".'], 400); }
        $query = TblSittingTableS::query();
        if ($status !== 'all') {$query->where('isReserved', $status === '1' ? 1 : 0);}
        if ($data) {   $query->where('data', $data);}
        $tables = $query->get();
        if ($tables->isEmpty()) {
            return response()->json(['message' => 'No tables found for the given criteria.'], 404);
        }
        return response()->json(['data' => $tables], 200); } 
        catch (\Exception $e) { return response()->json(['error' => 'Failed to fetch tables', 'details' => $e->getMessage()], 500);}
}

public function createReservationPayment(Request $request)
{
    try {
        $validatedData = $request->validate([
            'ReservationID' => 'required|exists:tblTableReservation,id',
            'ActualPrice' => 'required|numeric',
            'ExtraSeatPrice' => 'required|numeric',
            'ExtendedTimePrice' => 'required|numeric',
            'TotalTime' => 'required|date_format:H:i:s',
            'DiscountPercentage' => 'nullable|numeric|min:0',
            'DiscountPrice' => 'nullable|numeric|min:0',
            'TaxPercentage' => 'required|numeric|min:0',
            'TaxPrice' => 'required|numeric|min:0',
            'status' => 'required|in:pending,payment done,cancelled',
        ]);
        $validatedData['Added_By'] = auth()->user()->email;
        ReservationPayment::create($validatedData);
        return response()->json(['message' => 'Reservation Payment created successfully'], 200);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 400);
    }
}

public function updateReservationPayment(Request $request)
{
    try {
        $request->validate([
            'id' => 'required|integer|exists:tblReservationPayment,id',
            'ActualPrice' => 'nullable|numeric',
            'ExtraSeatPrice' => 'nullable|numeric',
            'ExtendedTimePrice' => 'nullable|numeric',
            'TotalTime' => 'nullable|numeric',
            'DiscountPercentage' => 'nullable|numeric',
            'DiscountPrice' => 'nullable|numeric',
            'TaxPercentage' => 'nullable|numeric',
            'TaxPrice' => 'nullable|numeric',
            'TotalPrice' => 'nullable|numeric',
            'status' => 'nullable|string|in:pending,payment done,cancelled',
        ]);
        $reservationPayment = ReservationPayment::find($request->id);
        if (!$reservationPayment) {  return response()->json(['message' => 'Reservation payment record not found'], 404);  }
        $input = $request->except('id');
        foreach ($input as $key => $value) {
            if ($reservationPayment->isFillable($key)) {
                $reservationPayment->$key = $value; } }
        $reservationPayment->Updated_By = auth()->user()->email;
        $reservationPayment->UpdatedDateTime = now();
        $reservationPayment->revision += 1;
        $reservationPayment->save();
        return response()->json(['message' => 'Reservation payment record updated successfully'], 200);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Error updating reservation payment record', 'error' => $e->getMessage()], 500);
    }
}


public function getReservationPayment(Request $request)
{
    try {
        $id = $request->query('id'); 
        if ($id) {
            $reservationPayment = ReservationPayment::where('ReservationID', $id)->first();
            if (!$reservationPayment) {
                return response()->json(['message' => 'No reservation payment record found for the given ID'], 404);
            }
            return response()->json(['data' => $reservationPayment], 200);
        }
        $reservationPayments = ReservationPayment::all();
        return response()->json(['data' => $reservationPayments], 200);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Error retrieving reservation payments', 'error' => $e->getMessage()], 500);
    }
}

public function deleteReservationPayment(Request $request)
{
    try {
        $id = $request->query('id');
        if (!$id || !is_numeric($id)) {
            return response()->json(['error' => 'Invalid or missing ID'], 400); }
        DB::table('tblReservationPayment')->where('id', $id)->delete();
        return response()->json(['message' => 'Reservation Payment deleted successfully'], 200);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 400);
    }
}

public function createBrand(Request $request)
    {
        try {
            $request->validate([
                'BrandName' => 'required|string|max:255',
            ]);
            $userEmail = Auth::user()->email;
            TblBrand::create([
                'BrandName' => $request->BrandName,
                'Added_By' => $userEmail,
                'AddedDateTime' => Carbon::now(),
            ]);
            return response()->json(['message' => 'Brand created successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create brand', 'details' => $e->getMessage()], 500);
        }
    }

    public function updateBrand(Request $request)
{
    try {
        $id = $request->query('id'); 
        if (!$id) { return response()->json(['error' => 'Brand ID is required'], 400); }
        $brand = TblBrand::findOrFail($id);
        $userEmail = Auth::user()->email;
        $brand->update([
            'BrandName' => $request->input('BrandName', $brand->BrandName),
            'Updated_By' => $userEmail,
            'UpdatedDateTime' => Carbon::now(),
            'Revision' => $brand->Revision + 1,
        ]);
        return response()->json(['message' => 'Brand updated successfully'], 200);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Failed to update brand', 'details' => $e->getMessage()], 500);
    }
}


    public function createCategory(Request $request)
    {
        try {
            $request->validate([
                'CategoryName' => 'required|string|max:255',
            ]);
            $userEmail = Auth::user()->email;
            TblCategory::create([
                'CategoryName' => $request->CategoryName,
                'Added_By' => $userEmail,
                'AddedDateTime' => Carbon::now(),
            ]);
            return response()->json(['message' => 'Category created successfully'], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create category', 'details' => $e->getMessage()], 500);
        }
    }

    public function updateCategory(Request $request)
{
    try {
        $id = $request->query('id'); // Get the id from query parameters
        $category = TblCategory::findOrFail($id);
        $userEmail = Auth::user()->email;
        $category->update([
            'CategoryName' => $request->input('CategoryName', $category->CategoryName),
            'Updated_By' => $userEmail,'UpdatedDateTime' => now(),'Revision' => $category->Revision + 1,
        ]);
        return response()->json(['message' => 'Category updated successfully'], 200);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Failed to update category', 'details' => $e->getMessage()], 500);
    }
}
    public function createSubCategory(Request $request)
    {
        try {
            $request->validate([
                'SubCategoryName' => 'required|string|max:255',
            ]);
            $userEmail = Auth::user()->email;
            TblSubCategory::create([
                'SubCategoryName' => $request->SubCategoryName,
                'Added_By' => $userEmail,
                'AddedDateTime' => Carbon::now(),
            ]);
            return response()->json(['message' => 'SubCategory created successfully'], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create sub-category', 'details' => $e->getMessage()], 500);
        }
    }

    public function updateSubCategory(Request $request)
{
    try {
        $id = $request->query('id');
        if (!$id) {  return response()->json(['error' => 'SubCategory ID is required'], 400); }
        $subCategory = TblSubCategory::findOrFail($id);
        $userEmail = Auth::user()->email;
        $subCategory->update([
            'SubCategoryName' => $request->input('SubCategoryName', $subCategory->SubCategoryName),
            'Updated_By' => $userEmail,'UpdatedDateTime' => Carbon::now(),  'Revision' => $subCategory->Revision + 1,
        ]);
        return response()->json(['message' => 'SubCategory updated successfully'], 200);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Failed to update sub-category', 'details' => $e->getMessage()], 500);
    }
}

    public function createSubSubCategory(Request $request)
    {
        try {
            $request->validate([
                'SubSubCategoryName' => 'required|string|max:255',
            ]);
            $userEmail = Auth::user()->email;
            TblSubSubCategory::create([
                'SubSubCategoryName' => $request->SubSubCategoryName,
                'Added_By' => $userEmail,
                'AddedDateTime' => Carbon::now(),
            ]);
            return response()->json(['message' => 'SubSubCategory created successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create sub-sub-category', 'details' => $e->getMessage()], 500);
        }
    }

    public function updateSubSubCategory(Request $request)
{
    try {
        $id = $request->query('id'); 
        $subSubCategory = TblSubSubCategory::findOrFail($id);
        $userEmail = Auth::user()->email;
        $subSubCategory->update([
            'SubSubCategoryName' => $request->input('SubSubCategoryName', $subSubCategory->SubSubCategoryName),
            'Updated_By' => $userEmail,'UpdatedDateTime' => Carbon::now(), 'Revision' => $subSubCategory->Revision + 1,
        ]);
        return response()->json(['message' => 'SubSubCategory updated successfully'], 200);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Failed to update sub-sub-category', 'details' => $e->getMessage()], 500);
    }
}

    
    public function createProduct(Request $request)
{
    try {
        $validator = Validator::make($request->all(), [
            'ProductCode' => 'required|string|max:255',
            'ProductName' => 'required|string|max:255',
            'CID' => 'nullable|integer',
            'SCID' => 'nullable|integer',
            'SSCID' => 'nullable|integer',
            'PurchasedPrice' => 'nullable|numeric',
            'SalePrice1' => 'nullable|numeric',
            'SalePrice2' => 'nullable|numeric',
            'DiscountPercentage' => 'nullable|numeric',
            'ActiveDiscount' => 'nullable|boolean',
            'ExpiryDate' => 'nullable|date',
            'RackNo' => 'nullable|string|max:255',
            'ReorderLevel' => 'nullable|integer',
            'Qty' => 'nullable|integer',
            'ImageName' => 'nullable|string|max:100', // Image name (string)
            'ImagePath' => 'required|url', // Validate the image URL
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation failed',
                'messages' => $validator->errors(),
            ], 400);
        }
        $imageUrl = $request->ImagePath;
        // Fetch the image from the URL
        $imageContent = file_get_contents($imageUrl);
        if ($imageContent === false) {
            return response()->json([
                'error' => 'Failed to fetch image from the URL',
            ], 400);
        }
        $imageName = time() . '_' . uniqid() . '.png'; // Adjust the extension as needed
        $imagePath = 'images/' . $imageName;
        Storage::disk('public')->put($imagePath, $imageContent);
        $product = TblProducts::create([
            'ProductCode' => $request->ProductCode,
            'ProductName' => $request->ProductName,
            'CID' => $request->CID,
            'SCID' => $request->SCID,
            'SSCID' => $request->SSCID,
            'PurchasedPrice' => $request->PurchasedPrice,
            'SalePrice1' => $request->SalePrice1,
            'SalePrice2' => $request->SalePrice2,
            'DiscountPercentage' => $request->DiscountPercentage,
            'ActiveDiscount' => $request->ActiveDiscount,
            'ExpiryDate' => $request->ExpiryDate,
            'RackNo' => $request->RackNo,
            'ReorderLevel' => $request->ReorderLevel,
            'Qty' => $request->Qty,
            'ImageName' => $request->ImageName,
            'ImagePath' => 'storage/' . $imagePath, // Save the image path in the database
            'Added_By' => Auth::user()->email,
            'AddedDateTime' => Carbon::now(),
        ]);
        return response()->json([
            'message' => 'Product added successfully',
            'data' => $product,
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'An error occurred while adding the product',
            'message' => $e->getMessage(),
        ], 500);
    }
}
   
public function updateProduct(Request $request)
{
    try {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
            'ProductCode' => 'nullable|string|max:255',
            'ProductName' => 'nullable|string|max:255',
            'CID' => 'nullable|integer',
            'SCID' => 'nullable|integer',
            'SSCID' => 'nullable|integer',
            'PurchasedPrice' => 'nullable|numeric',
            'SalePrice1' => 'nullable|numeric',
            'SalePrice2' => 'nullable|numeric',
            'DiscountPercentage' => 'nullable|numeric',
            'ActiveDiscount' => 'nullable|boolean',
            'ExpiryDate' => 'nullable|date',
            'RackNo' => 'nullable|string|max:255',
            'ReorderLevel' => 'nullable|integer',
            'Qty' => 'nullable|integer',
            'ImageName' => 'nullable|string|max:100',
            'ImagePath' => 'nullable|url',
        ]);
        if ($validator->fails()) { return response()->json(['error' => 'Validation failed', 'messages' => $validator->errors()], 400);}
        $product = TblProducts::find($request->query('id'));
        if (!$product) { return response()->json(['error' => 'Product not found'], 404);}
        if ($request->has('ImagePath')) {
            $imageContent = @file_get_contents($request->ImagePath);
            if ($imageContent === false) { return response()->json(['error' => 'Failed to fetch image from the URL'], 400);}
            $imageName = time() . '_' . uniqid() . '.png';
            $imagePath = 'images/' . $imageName;
            Storage::disk('public')->put($imagePath, $imageContent);
            $product->ImageName = $request->ImageName ?? $product->ImageName;
            $product->ImagePath = 'storage/' . $imagePath;
        }
        $fields = [
            'ProductCode', 'ProductName', 'CID', 'SCID', 'SSCID',
            'PurchasedPrice', 'SalePrice1', 'SalePrice2', 'DiscountPercentage',
            'ActiveDiscount', 'ExpiryDate', 'RackNo', 'ReorderLevel', 'Qty',
        ];
        foreach ($fields as $field) { if ($request->has($field)) { $product->$field = $request->$field;}}
        $product->Updated_By = Auth::user()->email;
        $product->save();
        return response()->json(['message' => 'Product updated successfully'], 200);
    } catch (\Exception $e) { return response()->json(['error' => 'An error occurred while updating the product', 'message' => $e->getMessage()], 500);}
}

public function searchProducts(Request $request)
{
    try {
        $query = $request->query('ProductName'); 
        if ($query) {
            $products = TblProducts::where('ProductName', 'LIKE', "%$query%")->get();
            if ($products->isEmpty()) { return response()->json(['message' => 'No products found matching the search criteria'], 404); }
            return response()->json(['products' => $products], 200);
        } else {
            $products = TblProducts::all();
            return response()->json(['products' => $products], 200); }
    } catch (\Exception $e) {return response()->json(['error' => 'Failed to fetch products', 'details' => $e->getMessage()], 500); }
}

public function deleteBrand(Request $request)
{
    try {
        $id = $request->query('id');
        $brand = TblBrand::findOrFail($id);
        $brand->delete();
        return response()->json(['message' => 'Brand deleted successfully'], 200);
    } catch (\Exception $e) {return response()->json(['error' => 'Failed to delete brand', 'details' => $e->getMessage()], 500);}
}
public function deleteCategory(Request $request)
{
    try {
        $id = $request->query('id'); 
        if (!$id) {  return response()->json(['error' => 'Category ID is required'], 400); }
        $category = TblCategory::findOrFail($id);
        $category->delete();
        return response()->json(['message' => 'Category deleted successfully'], 200);
    } catch (\Exception $e) {  return response()->json(['error' => 'Failed to delete category', 'details' => $e->getMessage()], 500);}
}

public function deleteSubCategory(Request $request)
{
    try {
        $id = $request->query('id');
        if (!$id) { return response()->json(['error' => 'SubCategory ID is required'], 400); }
        $subCategory = TblSubCategory::findOrFail($id);
        $subCategory->delete();
        return response()->json(['message' => 'SubCategory deleted successfully'], 200);
    } catch (\Exception $e) {return response()->json(['error' => 'Failed to delete subcategory', 'details' => $e->getMessage()], 500);}
}

public function deleteSubSubCategory(Request $request)
{
    try {
        $id = $request->query('id');
        if (!$id) {  return response()->json(['error' => 'SubSubCategory ID is required'], 400); }
        $subSubCategory = TblSubSubCategory::findOrFail($id);
        $subSubCategory->delete();
        return response()->json(['message' => 'SubSubCategory deleted successfully'], 200);
    } catch (\Exception $e) {  return response()->json(['error' => 'Failed to delete subsubcategory', 'details' => $e->getMessage()], 500); }
}

public function deleteProduct(Request $request)
{
    try {
        $id = $request->query('id');
        if (!$id) { return response()->json(['error' => 'Product ID is required'], 400); }
        $product = TblProducts::findOrFail($id);
        $product->delete();
        return response()->json(['message' => 'Product deleted successfully'], 200);
    } catch (\Exception $e) { return response()->json(['error' => 'Failed to delete product', 'details' => $e->getMessage()], 500);}
}

public function createAllPayment(Request $request)
{
    try {
        $request->validate([
            'ReservationID' => 'required|integer|exists:tbltablereservation,id',
            'ReservationPaymentID' => 'required|integer|exists:tblreservationpayment,id',
            'TotalPayment' => 'required|numeric|min:0',
            'CashPayment' => 'nullable|numeric|min:0',
            'CardPayment' => 'nullable|numeric|min:0',
            'InvoiceNo' => 'required|string|max:100|unique:tblallpayments,InvoiceNo',
        ]);
    } catch (\Illuminate\Validation\ValidationException $e) {
        // Check if specific fields failed validation
        $errors = $e->errors();
        if (isset($errors['ReservationID'])) {
            return response()->json(['message' => 'Reservation ID is invalid or not found'], 404);
        }
        if (isset($errors['ReservationPaymentID'])) {
            return response()->json(['message' => 'Reservation Payment ID is invalid or not found'], 404);
        }
        return response()->json(['message' => 'Validation error', 'errors' => $errors], 422);
    }

    try {
        $user = Auth::user();
        $userEmail = $user->email;
        $machineName = gethostname(); // Retrieves the host name of the server or machine
        $allPayment = TblAllPayments::create([
            'ReservationID' => $request->ReservationID,
            'ReservationPaymentID' => $request->ReservationPaymentID,
            'TotalPayment' => $request->TotalPayment,
            'CashPayment' => $request->CashPayment ?? 0,
            'CardPayment' => $request->CardPayment ?? 0,
            'InvoiceNo' => $request->InvoiceNo,
            'Added_By' => $userEmail,
            'MachineName' => $machineName,
        ]);
        return response()->json(['message' => 'Payment record added successfully'], 200);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Error adding payment record', 'error' => $e->getMessage()], 500);
    }
}

public function updateAllPayment(Request $request)
{
    try {
        $id = $request->query('id');
        if (!$id) { return response()->json(['message' => 'ID is required in query parameters'], 400); }
        $allPayment = TblAllPayments::findOrFail($id);
        $user = Auth::user();
        $userEmail = $user->email;
        $allPayment->update([
            'ReservationID' => $request->input('ReservationID', $allPayment->ReservationID),
            'ReservationPaymentID' => $request->input('ReservationPaymentID', $allPayment->ReservationPaymentID),
            'TotalPayment' => $request->input('TotalPayment', $allPayment->TotalPayment),
            'CashPayment' => $request->input('CashPayment', $allPayment->CashPayment),
            'CardPayment' => $request->input('CardPayment', $allPayment->CardPayment),
            'InvoiceNo' => $request->input('InvoiceNo', $allPayment->InvoiceNo),
            'Updated_By' => $userEmail,
            'UpdatedDateTime' => now(),
            'MachineName' => $request->input('MachineName', $allPayment->MachineName),
            'Revision' => $allPayment->Revision + 1,
        ]);
        return response()->json(['message' => 'Payment record updated successfully'], 200);
    } catch (\Exception $e) { return response()->json(['message' => 'Error updating payment record', 'error' => $e->getMessage()], 500);}
}


public function getAllPayments(Request $request)
{
    try {
        $id = $request->query('id');
        if ($id) {
             $allPayment = TblAllPayments::findOrFail($id);
            return response()->json(['message' => 'Payment record fetched successfully', 'data' => $allPayment], 200); }
        $allPayments = TblAllPayments::all();
        return response()->json(['message' => 'All payment records fetched successfully', 'data' => $allPayments], 200);
    } catch (\Exception $e) {return response()->json(['message' => 'Error fetching payment records', 'error' => $e->getMessage()], 500);}
}


public function deleteAllPayment(Request $request)
{
    try {
        $id = $request->query('id'); 
        if (!$id) {  return response()->json(['message' => 'Payment ID is required'], 400); }
        $allPayment = TblAllPayments::findOrFail($id);
        $allPayment->delete();
        return response()->json(['message' => 'Payment record deleted successfully'], 200);
    } catch (\Exception $e) { return response()->json(['message' => 'Error deleting payment record', 'error' => $e->getMessage()], 500);}
}


public function createVendor(Request $request)
{
    $request->validate([
        'VendorName' => 'required|string|max:255',
        'Email' => 'nullable|email|unique:tblVendor,Email',
        'Contact' => 'nullable|string|max:20',
        'Address' => 'nullable|string',
        'Fax' => 'nullable|string|max:50'
    ]);
    $user = Auth::user();
    $userEmail = $user->email;
    $machineName = gethostname(); // Retrieves the host name of the server or machine
    try {
        $vendor = Vendor::create([
            'VendorName' => $request->VendorName,
            'Email' => $request->Email,
            'Contact' => $request->Contact,
            'Address' => $request->Address,
            'Fax' => $request->Fax,
            'Added_By' => $userEmail,
            'AddedDateTime' => Carbon::now(),
            'MachineName' =>  $machineName
        ]);
        return response()->json(['message' => 'Vendor added successfully'], 200);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Error adding vendor','error' => $e->getMessage(),], 500);
    }
}

public function updateVendor(Request $request)
{
    $request->validate([
        'id' => 'required|integer|exists:tblVendor,id',
        'VendorName' => 'nullable|string|max:255',
        'Email' => 'nullable|email|unique:tblVendor,Email,' . $request->id,
        'Contact' => 'nullable|string|max:20',
        'Address' => 'nullable|string',
        'Fax' => 'nullable|string|max:50',
    ]);
    $user = Auth::user();
    $userEmail = $user->email;
    $machineName = gethostname(); 
    try {
        $vendor = Vendor::findOrFail($request->query('id'));
        $vendor->update([
            'VendorName' => $request->input('VendorName', $vendor->VendorName),
            'Email' => $request->input('Email', $vendor->Email),
            'Contact' => $request->input('Contact', $vendor->Contact),
            'Address' => $request->input('Address', $vendor->Address),
            'Fax' => $request->input('Fax', $vendor->Fax),
            'Updated_By' => $userEmail,
            'UpdatedDateTime' => Carbon::now(),
            'MachineName' => $machineName,
            'Revision' => $vendor->Revision + 1,
        ]);
        return response()->json(['message' => 'Vendor updated successfully'], 200);
    } catch (\Exception $e) { return response()->json(['message' => 'Error updating vendor', 'error' => $e->getMessage()], 500); }
}

public function getVendorDetails(Request $request)
{
    try {
        $id = $request->query('id');  
        if ($id) {
            $vendor = Vendor::findOrFail($id);
            return response()->json(['data' => $vendor], 200);
        } else {  $vendors = Vendor::all(); return response()->json(['data' => $vendors], 200); }
    } catch (\Exception $e) { return response()->json(['message' => 'Error fetching vendor data', 'error' => $e->getMessage()], 500); }
}

public function deleteVendor(Request $request)
{
    try {
        $id = $request->query('id'); // Retrieve 'id' from query params
        if (!$id) { return response()->json(['message' => 'Vendor ID is required'], 400); }
        $vendor = Vendor::findOrFail($id);
        $vendor->delete();
        return response()->json(['message' => 'Vendor deleted successfully'], 200);
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) { return response()->json(['message' => 'Vendor not found', 'error' => $e->getMessage()], 404);} 
        catch (\Exception $e) {return response()->json(['message' => 'Error deleting vendor', 'error' => $e->getMessage()], 500);}
}


public function createPurchaseMaster(Request $request)
{
    $request->validate([
        'VendorID' => 'required|integer|exists:tblvendor,id',
        'InvoiceNo' => 'required|string|max:255|unique:tblPurchaseMaster,InvoiceNo',
        'BatchNo' => 'nullable|string|max:255',
        'TotalTax' => 'nullable|numeric|min:0',
        'TotalDiscount' => 'nullable|numeric|min:0',
        'TotalAmount' => 'required|numeric|min:0',
        'MasterRemarks' => 'nullable|string',
        'Lock' => 'nullable|boolean',
        'IssuedtoStore' => 'nullable|boolean',
        'InvoiceDate' => 'nullable|date',
        'ActualDate' => 'nullable|date'
    ]);
    $user = Auth::user();
    $userEmail = $user->email;
    $machineName = gethostname(); 
    try {
        $purchaseMaster = PurchaseMaster::create([
            'VendorID' => $request->VendorID,
            'InvoiceNo' => $request->InvoiceNo,
            'BatchNo' => $request->BatchNo,
            'TotalTax' => $request->TotalTax,
            'TotalDiscount' => $request->TotalDiscount,
            'TotalAmount' => $request->TotalAmount,
            'MasterRemarks' => $request->MasterRemarks,
            'Lock' => $request->Lock ?? 0,
            'IssuedtoStore' => $request->IssuedtoStore ?? 0,
            'InvoiceDate' => $request->InvoiceDate,
            'ActualDate' => $request->ActualDate,
            'Stockin_By' => $userEmail,
            'Added_By' => $userEmail,
            'AddedDateTime' => Carbon::now(),
            'MachineName' => $machineName
        ]);
        return response()->json(['message' => 'Purchase master added successfully'], 200);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Error adding purchase master','error' => $e->getMessage() ], 500);
    }
}

public function updatePurchaseMaster(Request $request, $id)
{
    $request->validate([
        'VendorID' => 'nullable|integer|exists:tblvendor,id',
        'InvoiceNo' => 'nullable|string|max:255|unique:tblPurchaseMaster,InvoiceNo,' . $id,
        'BatchNo' => 'nullable|string|max:255',
        'TotalTax' => 'nullable|numeric|min:0',
        'TotalDiscount' => 'nullable|numeric|min:0',
        'TotalAmount' => 'nullable|numeric|min:0',
        'MasterRemarks' => 'nullable|string',
        'Lock' => 'nullable|boolean',
        'IssuedtoStore' => 'nullable|boolean',
        'InvoiceDate' => 'nullable|date',
        'ActualDate' => 'nullable|date'
    ]);
    $user = Auth::user();
    $userEmail = $user->email;
    $machineName = gethostname(); 
    try {
        $purchaseMaster = PurchaseMaster::findOrFail($id);
        $purchaseMaster->update([
            'VendorID' => $request->input('VendorID', $purchaseMaster->VendorID),
            'InvoiceNo' => $request->input('InvoiceNo', $purchaseMaster->InvoiceNo),
            'BatchNo' => $request->input('BatchNo', $purchaseMaster->BatchNo),
            'TotalTax' => $request->input('TotalTax', $purchaseMaster->TotalTax),
            'TotalDiscount' => $request->input('TotalDiscount', $purchaseMaster->TotalDiscount),
            'TotalAmount' => $request->input('TotalAmount', $purchaseMaster->TotalAmount),
            'MasterRemarks' => $request->input('MasterRemarks', $purchaseMaster->MasterRemarks),
            'Lock' => $request->input('Lock', $purchaseMaster->Lock),
            'IssuedtoStore' => $request->input('IssuedtoStore', $purchaseMaster->IssuedtoStore),
            'InvoiceDate' => $request->input('InvoiceDate', $purchaseMaster->InvoiceDate),
            'ActualDate' => $request->input('ActualDate', $purchaseMaster->ActualDate),
            'Updated_By' => $userEmail,
            'UpdatedDateTime' => Carbon::now(),
            'MachineName' => $machineName,
            'Revision' => $purchaseMaster->Revision + 1,
        ]);
        return response()->json(['message' => 'Purchase master updated successfully'], 200);
    } catch (\Exception $e) {
        return response()->json([ 'message' => 'Error updating purchase master', 'error' => $e->getMessage() ], 500);
    }
}
public function getPurchaseMasters(Request $request)
{
    try {
        $purchaseMasters = PurchaseMaster::all();
        return response()->json($purchaseMasters, 200);
    } catch (\Exception $e) {
        return response()->json([ 'message' => 'Error fetching purchase masters','error' => $e->getMessage()], 500);}
}
public function getPurchaseMasterById($id)
{
    try {
        $purchaseMaster = PurchaseMaster::findOrFail($id);
        return response()->json($purchaseMaster, 200);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Error fetching purchase master', 'error' => $e->getMessage() ], 404);}
}

public function deletePurchaseMaster($id)
{
    try {
        $purchaseMaster = PurchaseMaster::findOrFail($id);
        $purchaseMaster->delete();
         return response()->json([ 'message' => 'Purchase master deleted successfully' ], 200);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Error deleting purchase master','error' => $e->getMessage()  ], 404);}
}


}
