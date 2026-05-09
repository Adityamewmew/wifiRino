/**
 * Global error handler middleware
 */
function errorHandler(err, req, res, _next) {
  console.error('❌ Unhandled Error:', err.message || err);
  const status = err.status || err.statusCode || 500;
  res.status(status).json({
    success: false,
    message: err.message || 'Internal Server Error',
  });
}

module.exports = errorHandler;
