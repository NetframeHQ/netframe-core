# Emergency procedures

This document describes known production system problems with emergency resolve procedures.

When a new problem is discovered and resolved, the procedure should be added (after creating an issue to follow the development process) and the problem should be compiled at the [history section](#history) of this document.

The [symptoms section](#symptoms) describes visible symptoms on the platform and links to the [problems](#problems) section which describe technical issues and their possible solutions.


## How to follow this document

You need an SSH access to the impacted environment, ask the CTO for this.

Any command should be launched in the concerned environment.

With the [symptoms section](#symptoms), find the possible technical problem and follow instructions.

### After fixing a problem

If the symptom or the technical problem is not listed, when the problem is fixed, add everything needed to this document.

In any case, complete the [history section](#history) with the encountered problem.
New problems have to be written at the top of the history section.
The title shall be the date and time of the problem (in the `YYYY-MM-DD hh:mm` format, hours using the 24h format).

Open a pull request with you modifications.
This kind of updates need review too.
You can use a `task/*` branch for you pull request.


## Symptoms

This section describes "functionnal" bugs you can find and list possible technical sources of these problems.

### You can't register new data (profile posts, files, …)

* [hard-drive is full](#hard-drive-is-full)
* [Elastic Search is readonly](#elastic-search-is-readonly)


## Problems

This section covers technical problems and their possible solutions.


### Hard-drive is full

You can check hard drive capacity state with the command: `df -h`.

* [there is too much logs](#fix-too-much-logs)
* [a database filled up the drive with its data](#fix-large-database)

#### Fix too much logs

1. Try to find and purge useless files in `/tmp/`.
2. Try to find and purge useless logs in `/var/log/`.

#### Fix large database

We have no fix information to give, contact CTO.


### Elastic Search is readonly

* [hard-drive is full](#hard-drive-is-full)
* [Elastic Search locked itself](#fix-indexes-lock)

#### Fix indexes lock

1. Follow the [full hard-drive](#hard-drive-is-full) procedure, otherwise Elastic Search will lock back indexes.
2. Check if Elastic Search indexes are readonly. To do this, run `curl -X GET http://localhost:9200/_all/_settings` and see if a `"read_only_allow_delete":"true"` option appears.
3. If the option appears, unlock indexes. To do this, run `curl -X PUT -H 'Content-Type: application/json' -d '{"index.blocks.read_only_allow_delete":null}' http://localhost:9200/_all/_settings`.


## History

### 2020-12-23 ??:?? (morning)

Problem: Elastic Search was [readonly](#elastic-search-is-readonly).

Performed actions:

* [purge logs](#fix-too-much-logs)
* [unlock Elastic Search indexes](#fix-indexes-lock)
* create an issue to document these actions
* initiate this document
