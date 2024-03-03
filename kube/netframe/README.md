Netframe Helm chart
===================


Deploy
------

This chart is not deployed automatically in the Helm repository, instead when modified and review, we deploy it manually.


Values
------

You need to set some values.

Already present on `values.yaml`:

* `environment`: the deployment environment (defaults to `production`)
* `imagesRegistryUrl`: the Netframe registry URL
* `appProtocol`: the application protocol (`http` or `https`)
* `acmeEmail`: the email to send when issuing certificates with ACME protocol
* `appStorageNetframe`: the desired size for Netframe storage (uploads, instances CSS, …)
* `appReplicasNetframe`: the desired pods replication for the Netframe main application
* `appReplicasBroadcast`: the desired pods replication for the Netframe broadcast server
* `appReplicasCollab`: the desired pods replication for Netframe collaboratie notes server

Not present on `values.yaml` but required:

* `appVersionNetframe`: the Netframe main application image version to use
* `appVersionBroadcast`: the Netframe broadcast server image version to use
* `appVersionCollab`: the Netframe collaborative notes server image version to use
* `appHost`: the host of the application (`netframe.co`, `netframe.fr`, …)


About versions
--------------

The Helm chart version is fixed and updated with the chart.

The `appVersion` field is not provided as a chart version is used to deploy multiple Netframe application version.


Secrets
-------

When installed for the first time, secrets are blank and need to be filled.
The list of secrets to check is:

* `netframe-external`
* `netframe-netframe`
* `netframe-registry` (the `.dockerconfigjson` data needs to be a base64-encoded Docker auth file, generated with `docker login`)
* `netframe-acme` (only `ACME_EMAIL` and `OVH_`-prefixed fields shall be modified, others are automatically generated)
