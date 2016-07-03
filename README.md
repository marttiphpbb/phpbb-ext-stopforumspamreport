### phpBB 3.1 PhpBB Extension - marttiphpbb StopForumSpam Report

This extension makes it possible to submit spammers (or spambots) to the stopforumspam.com database. You need to obtain an api key from stopforumspam.

Blocking of Users by querying against the database of StopForumSpam.com is not provided, but this extension can be used alongside an extension that does. Like i.e. [https://github.com/RMcGirr83/phpBB-3.1-stopforumspam]

#### Quick Install

You can install this on the latest release of phpBB 3.1 by following the steps below:

* Create `marttiphpbb/stopforumspamreport` in the `ext` directory.
* Download and unpack the repository into `ext/marttiphpbb/stopforumspamreport`
* Enable `StopForumSpam Report` in the ACP at `Customise > Manage extensions`.
* Fill in the api key you obtained from stopforumspam.com in `Extensions > StopForumSpam Report`

#### How to Use

* When you want to delete a user in the ACP, an option `Report to StopForumSpam` is provided.

#### Uninstall

* Disable `StopForumSpam Report` in the ACP at `Customise -> Extension Management -> Extensions`.
* To permanently uninstall, click `Delete Data`.
* Optionally delete the `/ext/marttiphpbb/stopforumspamreport` directory.

#### Support

* **Important: Only official release versions validated by the phpBB Extensions Team should be installed on a live forum. Pre-release (beta, RC) versions downloaded from this repository are only to be used for testing on offline/development forums and are not officially supported.**
* Report bugs and other issues to the [Issue Tracker](https://github.com/marttiphpbb/phpbb-ext-stopforumspamreport/issues).
* Support requests should be posted and discussed in the [StopForumSpam Report topic at phpBB.com](https://www.phpbb.com/community/viewtopic.php?f=456&t=2334431).

#### License

[GPL-2.0](license.txt)
