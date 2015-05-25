<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Http\Requests\PrepareNoticeRequest;
use App\Provider;
use Illuminate\Auth\Guard;
use Illuminate\Http\Request;

class NoticesController extends Controller {

	public function __construct()
	{
		$this->middleware('auth');
	}

	public function index()
	{
		return 'all notices';
	}

	public function create()
	{
		// get list of providers
		$providers = Provider::lists('name', 'id');

		// load a view to create a new notice
		return view('notices.create', compact('providers'));
	}

	/**
	 * Ask the user to confirm the DMCA that will be delivered
	 *
	 * @param PrepareNoticeRequest $request
	 * @param Guard $auth
	 * @return \Illuminate\View\View
	 */
	public function confirm(PrepareNoticeRequest $request, Guard $auth)
	{
		$template = $this->compileDmcaTemplate($data = $request, $auth);

		session()->flash('dmca', $data->all());

		return view('notices.confirm', compact('template'));
	}

	public function store()
	{
		// Form data is flashed. Get with session()->get('dmca')
		// Template is in request. Request::input('template')
		// So build up a Notice object (create table too)
		// persist it with this data
		// And then fire off the email
	}

	/**
	 * Compile the DMCA template from the form data
	 *
	 * @param PrepareNoticeRequest $data
	 * @param Guard $auth
	 * @return mixed
	 */
	private function compileDmcaTemplate(PrepareNoticeRequest $data, Guard $auth)
	{
		$data = $data->all() + [
				'name' => $auth->user()->name,
				'email' => $auth->user()->email
			];

		$template = view()->file(app_path('Http/Templates/dmca.blade.php'), $data);
		return $template;
	}

}
