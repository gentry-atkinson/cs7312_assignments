#include <fstream>
#include <iostream>
#include <stdio.h>
#include <cmath>
#include <limits>

using namespace std;

struct point{
	float x;
	float y;
	int cluster;
};

float euDis(point, point);

int main(int argc, char** argv){
	if (argc != 4) {
		cerr << "Command format is \"dunnIndex num_points num_clusters filename\"" << endl;
		return 1;
	}

    numeric_limits<float> bound;
	int numPoints = atoi(argv[1]);
	int numClusters = atoi(argv[2]);
	ifstream inFile(argv[3]);

	point points[numPoints];
	point centers[numClusters];
	int temp = 0;
	while (!inFile.eof()){
        inFile >> points[temp].x >> points[temp].y >> points[temp].cluster;
        temp++;
	}

	//calculate centers
	float xSum[numClusters];
    float ySum[numClusters];
    float totalPoints[numClusters];
	for(int i = 0; i < numClusters; i++){
        xSum[i] = 0;
        ySum[i] = 0;
        totalPoints[i] = 0;
	}
	for (int i = 0; i < numPoints; i++){
        xSum[points[i].cluster-1] += points[i].x;
        ySum[points[i].cluster-1] += points[i].y;
        totalPoints[points[i].cluster-1] += 1;
	}
	for (int i = 0; i < numClusters; i++){
        centers[i].x = (xSum[i] / totalPoints[i]);
        centers[i].y = (ySum[i] / totalPoints[i]);
        centers[i].cluster = i+1;
	}

	float minInterClusterDist = bound.max();
	float maxIntraClusterDist = 0;

	//calculate min inter-cluster distance
	for (int i = 0; i < numClusters; i++){
        for (int j = i; j < numClusters; j++){
            if (i == j) continue;
            float thisDist = euDis(centers[i], centers[j]);
            if (thisDist < minInterClusterDist)
                minInterClusterDist = thisDist;
        }
	}

	//calculate max intra-cluster distance
	for (int i = 0; i < numPoints; i++){
        for (int j = i; j < numPoints; j++){
            if (points[i].cluster == points[j].cluster){
                float thisDis = euDis(points[i], points[j]);
                if (thisDis > maxIntraClusterDist)
                    maxIntraClusterDist = thisDis;
            }
        }
	}


    cout << minInterClusterDist/maxIntraClusterDist << endl;

	return 0;
}

float euDis(point p, point c){
	float dist = pow((p.x - c.x), 2) + pow((p.y - c.y), 2);
	dist = sqrt(dist);
	return dist;
}
