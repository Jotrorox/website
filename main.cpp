#include "httplib.h"
#include "logger.hpp"

#include <iostream>
#include <thread>
#include <vector>

int main() {
    auto& logger = slog::Logger::instance();

    logger.set_level(slog::Level::warning);
    logger.enable_colors(true);
    logger.enable_console(true);

    httplib::Server svr;

    svr.set_mount_point("/", "./public");

    SLOG_INFO("The server now started on: ", 8080);

    svr.listen("0.0.0.0", 8080);
}