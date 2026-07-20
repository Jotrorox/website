cmake_minimum_required(VERSION 3.8)

project(server LANGUAGES CXX)

add_executable(server main.cpp)
target_compile_features(server PRIVATE cxx_std_17)
target_include_directories(server PRIVATE include)
target_compile_options(server PRIVATE -O2)
find_package(Threads REQUIRED)
target_link_libraries(server PRIVATE Threads::Threads)
