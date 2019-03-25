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
		cerr << "Command format is \"hubertGamma num_points num_clusters filename\"" << endl;
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

    float M = (numPoints * (numPoints - 1)) / 2;
    float hgs = 0;
    for (int i = 0; i < numPoints-1; i++){
        for (int j = i+1; j < numPoints; j++){
            if (points[i].cluster == points[j].cluster) continue;
            hgs += euDis(points[i], points[j]) * euDis(centers[points[i].cluster], centers[points[j].cluster]);
        }
    }

    hgs /= M;

    cout << hgs << endl;

	return 0;
}

float euDis(point p, point c){
	float dist = pow((p.x - c.x), 2) + pow((p.y - c.y), 2);
	dist = sqrt(dist);
	return dist;
}
