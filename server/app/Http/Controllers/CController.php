<?php

namespace App\Http\Controllers;

use App\Models\Connection;
use Illuminate\Http\Request;
use LDAP;
use LDAPCFG;

class CController extends Controller
{
    /**
     *
     *
     * @param Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function create(Request $request)
    {
        $fields = $request->validate([
            'name' => 'string|required|min:3',
            'description' => 'string',
            'private' => 'integer|required',
            'host' => 'required|string',
            'base_dn' => 'required|string',
            'bind_dn' => 'string',
            'bind_pw' => 'string',
            'port' => 'integer',
            'tls' => 'integer',
            'version' => 'integer',
            'encoding' => 'string',
            'pagesize' => 'integer',
            'pagedresultscontrol' => 'string',
            'rootdse' => 'string',
        ]);

        $tls = false;
        if (key_exists('tls', $fields) && $fields['tls'] === 1) {
            $tls = true;
        }

        $ldapcfg = new LDAPCFG(
            $fields['host'],
            $fields['base_dn'],
            $request->input('bind_dn') ?? '',
            $request->input('bind_pw') ?? '',
            $request->input('port') ?? LDAP_DEFAULT_PORT,
            $tls,
            $request->input('version') ?? LDAP_DEFAULT_VERSION,
            $request->input('encoding') ??  LDAP_DEFAULT_ENCODING,
            $request->input('pagesize') ?? LDAP_DEFAULT_PAGESIZE,
            $request->input('pagedresultscontrol') ?? LDAP_DEFAULT_PAGED_RESULTS_CONTROL,
            $request->input('rootdse') ?? LDAP_DEFAULT_ROOTDSE,
        );

        $user = $request->user();

        $ldap = new LDAP($ldapcfg, $user);
        if ($ldap->isValid()) {
            Connection::create([
                'name' => $fields['name'],
                'description' => $request->input('description') ?? '',
                'host' => $fields['host'],
                'base_dn' => $fields['base_dn'],
                'bind_dn' => $request->input('bind_dn') ?? '',
                'bind_pw' => $request->input('bind_pw') ?? '',
                'port' => $request->input('port') ?? LDAP_DEFAULT_PORT,
                'tls' => $tls,
                'version' => $request->input('version') ?? LDAP_DEFAULT_VERSION,
                'encoding' => $request->input('encoding') ??  LDAP_DEFAULT_ENCODING,
                'pagesize' => $request->input('pagesize') ?? LDAP_DEFAULT_PAGESIZE,
                'pagedresultscontrol' => $request->input('pagedresultscontrol') ?? LDAP_DEFAULT_PAGED_RESULTS_CONTROL,
                'rootdse' => $request->input('rootdse') ?? LDAP_DEFAULT_ROOTDSE,
                'private' => $request->boolean('private') ?? 0,
                'user' => $user->id,
            ]);

            return response(['msg' => $ldap->getMessage()], 400);
        }

        return response('', 400);
    }

    /**
     *
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function update(Request $request, $id)
    {
        $model = Connection::find($id);
        if ($model) {
            $user = $request->user();

            if ($model->user !== $user->id) {
                return response('', 403);
            }

            $fields = $request->validate([
                'name' => 'string|required|min:3',
                'description' => 'string',
                'private' => 'integer|required',
                'host' => 'required|string',
                'base_dn' => 'required|string',
                'bind_dn' => 'string',
                'bind_pw' => 'string',
                'port' => 'integer',
                'tls' => 'integer',
                'version' => 'integer',
                'encoding' => 'string',
                'pagesize' => 'integer',
                'pagedresultscontrol' => 'string',
                'rootdse' => 'string',
            ]);


            $tls = false;
            if (key_exists('tls', $fields) && $fields['tls'] === 1) {
                $tls = true;
            }

            $ldapcfg = new LDAPCFG(
                $fields['host'],
                $fields['base_dn'],
                $request->input('bind_dn') ?? '',
                $request->input('bind_pw') ?? '',
                $request->input('port') ?? LDAP_DEFAULT_PORT,
                $tls,
                $request->input('version') ?? LDAP_DEFAULT_VERSION,
                $request->input('encoding') ??  LDAP_DEFAULT_ENCODING,
                $request->input('pagesize') ?? LDAP_DEFAULT_PAGESIZE,
                $request->input('pagedresultscontrol') ?? LDAP_DEFAULT_PAGED_RESULTS_CONTROL,
                $request->input('rootdse') ?? LDAP_DEFAULT_ROOTDSE,
            );

            $ldap = new LDAP($ldapcfg, $user);
            if ($ldap->isValid()) {
                $model->name =  $fields['name'];
                $model->description =  $request->input('description') ?? $model->description;
                $model->host =  $fields['host'];
                $model->base_dn =  $fields['base_dn'];
                $model->bind_dn =  $request->input('bind_dn') ?? $model->bind_dn;
                $model->bind_pw =  $request->input('bind_pw') ?? $model->bind_pw;
                $model->port =  $request->input('port') ?? $model->port;
                $model->version =  $request->input('version') ?? $model->version;
                $model->encoding =  $request->input('encoding') ?? $model->encoding;
                $model->pagesize =  $request->input('pagesize') ?? $model->pagesize;
                $model->pagedresultscontrol =  $request->input('pagedresultscontrol') ?? $model->pagedresultscontrol;
                $model->rootdse =  $request->input('rootdse') ?? $model->rootdse;

                $model->save();
                return response('', 200);
            }

            return response(['msg' => $ldap->getMessage()], 400);
        }
        return response('', 404);
    }

    /**
     *
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function delete(Request $request, $id)
    {
        $model = Connection::find($id);
        if ($model) {
            $user = $request->user();
            if ($model->user !== $user->id) {
                return response('', 403);
            }
            $model->delete();
            return response('', 200);
        }
        return response('', 404);
    }

    /**
     *
     *
     * @param Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function test(Request $request)
    {
        $fields = $request->validate([
            'host' => 'required|string',
            'base_dn' => 'required|string',
            'bind_dn' => 'string',
            'bind_pw' => 'string',
            'port' => 'integer',
            'tls' => 'integer',
            'version' => 'integer',
            'encoding' => 'string',
            'pagesize' => 'integer',
            'pagedresultscontrol' => 'string',
            'rootdse' => 'string',
        ]);

        $tls = false;
        if (key_exists('tls', $fields) && $fields['tls'] === 1) {
            $tls = true;
        }

        $ldapcfg = new LDAPCFG(
            $fields['host'],
            $fields['base_dn'],
            $request->input('bind_dn') ?? '',
            $request->input('bind_pw') ?? '',
            $request->input('port') ?? LDAP_DEFAULT_PORT,
            $tls,
            $request->input('version') ?? LDAP_DEFAULT_VERSION,
            $request->input('encoding') ??  LDAP_DEFAULT_ENCODING,
            $request->input('pagesize') ?? LDAP_DEFAULT_PAGESIZE,
            $request->input('pagedresultscontrol') ?? LDAP_DEFAULT_PAGED_RESULTS_CONTROL,
            $request->input('rootdse') ?? LDAP_DEFAULT_ROOTDSE,
        );

        $user = $request->user();

        $ldap = new LDAP($ldapcfg, $user);
        $response = ['msg' => $ldap->test()];
        return response($response, 200);
    }
}
