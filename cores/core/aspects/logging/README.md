# logging

This folder contains an ErrorLogger and a RequestLogger.

## Errorlogger

The errorlogger is in the first place something that catches exceptions thrown in the framework for logging purposes. But, it also functions as a handler for fatal errors in the framework, making it so that all errors are guided to our errorlogger.

## RequestLogger

The requestlogger is called upon every time someone does a request to The DataTank. Currently only GET-requests are being logged, current RFC is on the issue list though to expand the functionality to support the storage of the HTTP request method.

