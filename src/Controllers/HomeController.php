<?php
namespace TRT\Service\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use TRT\Service\Repositories\InitRepository;

class HomeController extends Controller {
	protected $request;
	protected $repo;

	public function __construct(
		Request $request,
		InitRepository $repo
	) {
		$this->request = $request;
		$this->repo = $repo;
	}

	public function about() {
		return $this->repo->product();
	}

	/**
	 * Used to get right sidebar content
	 */
	public function helpDoc() {
		return $this->repo->helpDoc(request('subject'));
	}
}
