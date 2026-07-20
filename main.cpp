#include "httplib.h"
#include "logger.hpp"

#include <cstdlib>
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

    const char* port_env = std::getenv("PORT");
    const int port = port_env ? std::stoi(port_env) : 8080;

    SLOG_INFO("The server now started on: ", port);

    svr.listen("0.0.0.0", port);
}
