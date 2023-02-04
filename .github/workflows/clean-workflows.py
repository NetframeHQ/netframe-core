#!/usr/bin/env python3

##
# This script remove all workflows but latest for each branch
##

from os import getenv
from requests import request

GITHUB_ENDPOINT = 'https://api.github.com'
GITHUB_TOKEN = getenv('GITHUB_TOKEN')
GITHUB_REPOSITORY = getenv('GITHUB_REPOSITORY')

if GITHUB_TOKEN is None:
    raise Exception("The GITHUB_TOKEN environment variable is required")

if GITHUB_REPOSITORY is None:
    raise Exception("The GITHUB_REPOSITORY environment variable is required")

API_REPO_PATH = '/repos/' + GITHUB_REPOSITORY


def request_api(method, path, *args, **kwargs):
    url = GITHUB_ENDPOINT + path
    return request(method=method, url=url, headers={
        'Accept': 'application/vnd.github.v3+json',
        'Authorization': 'token ' + GITHUB_TOKEN
    }, *args, **kwargs)


def list_runs(page=0):
    resp = request_api('GET', API_REPO_PATH + '/actions/runs', params={
        'page': page,
        'per_page': 100
    })

    runs = list(resp.json()['workflow_runs'])

    if len(runs) >= 100:
        return runs + list_runs(page + 1)

    return runs


def delete_workflow_run(id):
    workflow_id = str(id)
    request_api('DELETE', API_REPO_PATH + '/actions/runs/' + workflow_id)
    print('Removed workflow ' + workflow_id)


def group_runs(raw_runs):
    runs = {}

    for run in raw_runs:
        branch = run['head_branch']
        runs.setdefault(branch, {})

        workflow_id = run['workflow_id']
        runs[branch].setdefault(workflow_id, [])

        runs[branch][workflow_id].append({
            'id': run['id'],
            'date': run['created_at']
        })

    return runs


def list_obsoletes_runs(workflow_id, runs):
    if len(runs) <= 1:
        return []

    ordered_workflow_runs = sorted(
        runs,
        key=lambda run: run['date'],
        reverse=True
    )
    ordered_workflow_runs.pop(0)

    return map(lambda run: run['id'], ordered_workflow_runs)


def clean_runs(runs):
    for workflow_id in runs:
        runs_to_remove = list_obsoletes_runs(
            workflow_id,
            runs[workflow_id]
        )

        for run in runs_to_remove:
            delete_workflow_run(run)


def run():
    runs = group_runs(list_runs())

    for branch in runs:
        clean_runs(runs[branch])


if __name__ == '__main__':
    run()
