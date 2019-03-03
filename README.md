# Przeslijmi Sirouter

Simple routing solution.

## Description

Register and call routes.

## How to register route

```
// simplest
Sirouter::register('/called-uri')->setCall('Namespace\Space\Class', 'get');

// for PUT HTTP request
Sirouter::register('/called-uri', 'PUT')->setCall('Namespace\Space\Class', 'put');

// with parameter
Sirouter::register('/called-uri/(\d+)')
    ->setCall('Namespace\Space\Class', 'put')
    ->setParam(0, 'id');
```

## How to call registered route

```
Sirouter::call($_SERVER['PATH_INFO'], $_SERVER['REQUEST_METHOD'], $_SERVER['QUERY_STRING']);
```