<?php
namespace TRT\Service\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use TRT\Service\Repositories\InstallRepository;
use TRT\Service\Requests\InstallRequest;

class InstallController extends Controller {
	protected $repo;
	protected $request;

	/**
	 * Instantiate a new controller instance.
	 *
	 * @return void
	 */
	public function __construct(
		InstallRepository $repo,
		Request $request
	) {
		$this->repo = $repo;
		$this->request = $request;
	}

	/**
	 * Used to get pre requisites of server and folder
	 */
	public function preRequisite() {
		$checks = $this->repo->getPreRequisite();

		$server_checks = $checks['server'];
		$folder_checks = $checks['folder'];
		$verifier = $checks['verifier'];

		envu(['APP_ENV' => 'local']);
		$name = env('APP_NAME');

		return $this->success(compact('server_checks', 'folder_checks', 'name', 'verifier'));
	}

	/**
	 * Used to install the application
	 */
	public function store(InstallRequest $request, $option = null) {
		$valid_database = $this->repo->validateDatabase($this->request->all(), $option);

		if ($option === 'database' || $option === 'admin' || $option === 'access_code') {
			return $this->success([]);
		}

		$this->repo->install($this->request->all());

		return $this->success(['message' => trans('install.done')]);
	}
}
