function distanceInMeters(start, end) {
    const earthRadius = 6_371_000;
    const latitudeDifference = (end[0] - start[0]) * Math.PI / 180;
    const longitudeDifference = (end[1] - start[1]) * Math.PI / 180;
    const a =
        Math.sin(latitudeDifference / 2) ** 2
        + Math.cos(start[0] * Math.PI / 180)
        * Math.cos(end[0] * Math.PI / 180)
        * Math.sin(longitudeDifference / 2) ** 2;

    return 2 * earthRadius * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
}

export function getVideoRouteProgress(latitude, longitude, route) {
    const segments = [];
    let totalLength = 0;

    for (let index = 1; index < route.length; index += 1) {
        const start = route[index - 1];
        const end = route[index];
        const length = distanceInMeters(start, end);

        segments.push({ start, end, length, startDistance: totalLength });
        totalLength += length;
    }

    if (totalLength === 0) return 0;

    let closestDistance = Infinity;
    let routeDistance = 0;

    segments.forEach(({ start, end, length, startDistance }) => {
        const referenceLatitude = (start[0] + end[0] + latitude) / 3 * Math.PI / 180;
        const startX = start[1] * Math.PI / 180 * Math.cos(referenceLatitude);
        const startY = start[0] * Math.PI / 180;
        const endX = end[1] * Math.PI / 180 * Math.cos(referenceLatitude);
        const endY = end[0] * Math.PI / 180;
        const pointX = longitude * Math.PI / 180 * Math.cos(referenceLatitude);
        const pointY = latitude * Math.PI / 180;
        const deltaX = endX - startX;
        const deltaY = endY - startY;
        const segmentLengthSquared = deltaX ** 2 + deltaY ** 2;
        const segmentProgress = segmentLengthSquared > 0
            ? Math.max(0, Math.min(1, ((pointX - startX) * deltaX + (pointY - startY) * deltaY) / segmentLengthSquared))
            : 0;
        const projectedX = startX + deltaX * segmentProgress;
        const projectedY = startY + deltaY * segmentProgress;
        const distanceSquared = (pointX - projectedX) ** 2 + (pointY - projectedY) ** 2;

        if (distanceSquared < closestDistance) {
            closestDistance = distanceSquared;
            routeDistance = startDistance + length * segmentProgress;
        }
    });

    return Math.max(0, Math.min(1, routeDistance / totalLength));
}
