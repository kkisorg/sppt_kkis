<?php

namespace App\Http\Controllers;

use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use Imagick;

use App\Document;
use App\UserActivityTracking;

class DocumentController extends Controller
{
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the list of document
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        // Track user activity
        UserActivityTracking::create([
            'user_id' => Auth::id(),
            'activity_type' => 'DISPLAY',
            'activity_details' => 'DOCUMENT_INDEX',
            'full_url' => $request->fullUrl(),
            'method' => $request->method(),
            'is_ajax' => $request->ajax(),
            'is_secure' => $request->secure(),
            'ip' => $request->ip(),
            'header' => json_encode($request->header()),
        ]);

        // Non-admin cannot perform this action
        $user = Auth::user();
        if (!$user->is_admin) {
            abort(403);
        }
        $documents = Document::orderBy('publish_timestamp', 'desc')->get();
        foreach ($documents as $document) {
            $document->publish_datetime =
                Carbon::createFromTimestamp($document->publish_timestamp)->format('j F Y');
        }
        return view('document.index', ['documents' => $documents]);
    }

    /**
     * Display the create document form
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request)
    {
        // Track user activity
        UserActivityTracking::create([
            'user_id' => Auth::id(),
            'activity_type' => 'DISPLAY',
            'activity_details' => 'DOCUMENT_CREATE',
            'full_url' => $request->fullUrl(),
            'method' => $request->method(),
            'is_ajax' => $request->ajax(),
            'is_secure' => $request->secure(),
            'ip' => $request->ip(),
            'header' => json_encode($request->header()),
        ]);

        // Non-admin cannot perform this action
        $user = Auth::user();
        if (!$user->is_admin) {
            abort(403);
        }
        return view('document.create');
    }

    /**
     * Insert a new document into the database
     *
     * @param Request $request
     * @return Response
     */
    public function insert(Request $request)
    {
        // Track user activity
        UserActivityTracking::create([
            'user_id' => Auth::id(),
            'activity_type' => 'CLICK',
            'activity_details' => 'DOCUMENT_CREATE',
            'full_url' => $request->fullUrl(),
            'method' => $request->method(),
            'is_ajax' => $request->ajax(),
            'is_secure' => $request->secure(),
            'ip' => $request->ip(),
            'header' => json_encode($request->header()),
        ]);

        // Non-admin cannot perform this action
        $user = Auth::user();
        if (!$user->is_admin) {
           abort(403);
        }

        $now = Carbon::now();

        $document_type = $request->input('document-type');
        $name = $request->input('name');
        $publish_datetime = $request->input('publish-datetime');
        $publish_timestamp = Carbon::parse($publish_datetime)->timestamp;

        $file = $request->file('document');
        $original_filename = $file->getClientOriginalName();
        $file_extension = strtolower($file->getClientOriginalExtension());
        $file_size = $file->getClientSize();

        // Only PDF is allowed
        $supported_extension = array('pdf');
        if (!in_array($file_extension, $supported_extension)) {
            return redirect('/document', 303)
                ->with('error_message', 'Hanya PDF dokumen yang dapat diterima.');
        }
        // Document size is 5MB maximum
        if ($file_size > 5242880) {
            return redirect('/document', 303)
                ->with('error_message', 'Dokumen terlalu besar (maksimal 5MB).');
        }

        // Upload document
        $filename = Auth::id() . '_' . $now->timestamp . '_' . preg_replace('/\s+/', '_', $original_filename);
        $file_path = Storage::putFileAs('public/documents', $file, $filename);

        $document = Document::create([
            'type' => $document_type,
            'name' => $name,
            'file_path' => $file_path,
            'publish_timestamp' => $publish_timestamp,
            'creator_id' => Auth::id(),
        ]);

        return redirect('/document', 303)
            ->with('success_message', 'Dokumen telah berhasil dibuat.');
    }

    /**
     * Display the edit document form
     *
     * @param Request $request
     * @param string $document_id
     * @return Response
     */
    public function edit(Request $request, string $document_id)
    {
        // Track user activity
        UserActivityTracking::create([
            'user_id' => Auth::id(),
            'activity_type' => 'DISPLAY',
            'activity_details' => 'DOCUMENT_EDIT',
            'full_url' => $request->fullUrl(),
            'method' => $request->method(),
            'is_ajax' => $request->ajax(),
            'is_secure' => $request->secure(),
            'ip' => $request->ip(),
            'header' => json_encode($request->header()),
        ]);

        // Non-admin cannot perform this action
        $user = Auth::user();
        if (!$user->is_admin) {
            abort(403);
        }
        $document = Document::findOrFail($document_id);
        $document->publish_datetime =
            Carbon::createFromTimestamp($document->publish_timestamp)->format('m/d/Y');
        return view('document.edit', ['document' => $document]);
    }

    /**
     * Update a document into the database
     *
     * @param Request $request
     * @return Response
     */
    public function update(Request $request)
    {
        // Track user activity
        UserActivityTracking::create([
            'user_id' => Auth::id(),
            'activity_type' => 'CLICK',
            'activity_details' => 'DOCUMENT_CREATE',
            'full_url' => $request->fullUrl(),
            'method' => $request->method(),
            'is_ajax' => $request->ajax(),
            'is_secure' => $request->secure(),
            'ip' => $request->ip(),
            'header' => json_encode($request->header()),
        ]);

        // Non-admin cannot perform this action
        $user = Auth::user();
        if (!$user->is_admin) {
           abort(403);
        }

        $now = Carbon::now();

        $id = $request->input('id');
        $document_type = $request->input('document-type');
        $name = $request->input('name');
        $publish_datetime = $request->input('publish-datetime');
        $publish_timestamp = Carbon::parse($publish_datetime)->timestamp;

        Document::where('id', $id)->update([
            'type' => $document_type,
            'name' => $name,
            'publish_timestamp' => $publish_timestamp,
            'editor_id' => Auth::id(),
        ]);

        if ($request->hasFile('document')) {
            // First, delete the previous file
            $document = Document::findOrFail($id);
            $old_file_path = $document->file_path;
            Storage::delete($old_file_path);

            // Get the file
            $file = $request->file('document');
            $original_filename = $file->getClientOriginalName();
            $file_extension = strtolower($file->getClientOriginalExtension());
            $file_size = $file->getClientSize();

            // Only PDF is allowed
            $supported_extension = array('pdf');
            if (!in_array($file_extension, $supported_extension)) {
                return redirect('/document', 303)
                    ->with('error_message', 'Hanya PDF dokumen yang dapat diterima.');
            }
            // Document size is 5MB maximum
            if ($file_size > 5242880) {
                return redirect('/document', 303)
                    ->with('error_message', 'Dokumen terlalu besar (maksimal 5MB).');
            }

            // Upload document
            $filename = Auth::id() . '_' . $now->timestamp . '_' . preg_replace('/\s+/', '_', $original_filename);
            $file_path = Storage::putFileAs('public/documents', $file, $filename);

            // Store the new file path
            Document::where('id', $id)->update(['file_path' => $file_path]);
        }

        return redirect('/document', 303)
            ->with('success_message', 'Dokumen telah berhasil dibuat.');
    }

    /**
     * Delele a document from the database
     *
     * @param Request $request
     * @return Response
     */
    public function delete(Request $request, string $document_id)
    {
        // Track user activity
        UserActivityTracking::create([
            'user_id' => Auth::id(),
            'activity_type' => 'CLICK',
            'activity_details' => 'DOCUMENT_DELETE',
            'full_url' => $request->fullUrl(),
            'method' => $request->method(),
            'is_ajax' => $request->ajax(),
            'is_secure' => $request->secure(),
            'ip' => $request->ip(),
            'header' => json_encode($request->header()),
        ]);

        // Non-admin cannot perform this action
        $user = Auth::user();
        if (!$user->is_admin) {
            abort(403);
        }
        // First, delete the file
        $document = Document::findOrFail($document_id);
        $file_path = $document->file_path;
        Storage::delete($file_path);

        Document::destroy($document_id);
        return redirect('/document', 303)
            ->with('success_message', 'Dokumen telah berhasil dihapus.');
    }
}
