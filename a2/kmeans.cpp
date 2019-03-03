#include <fstream>
#include <iostream>
#include <string>
#include <stdio.h>
#include <cstdlib>
#include <ctime>
#include <cmath>
#include <cfloat>

using namespace std;

struct point{
	float x;
	float y;
	int cluster;
};

struct centroid{
	float x;
	float y;
};

float euDis(point, centroid);

int main(int argc, char** argv){
	if (argc != 3) {
		cerr << "Command format is \"kmeans k input_file\"" << endl;
		return 1;
	}

	int listSize = 10;
	point* pointList = new point[listSize];
	int numberOfPoints = 0;
	int k = atoi(argv[1]);
	string inFileName = argv[2];
	ifstream inFile(inFileName);
	centroid * centroidList = new centroid[k];
	//float maxX = 0, maxY = 0;
	bool done;
	//int numLoops = 0;
	string palette[] = {"red", "blue", "green", "yellow"};

	//Read in the input file
	while (!inFile.eof()){
		//max list size has been reached. Expand
		if (numberOfPoints >= listSize){
			listSize *= 2;
			point * newList = new point[listSize];
			for (int i = 0; i < numberOfPoints; i++){
				newList[i].x = pointList[i].x;
				newList[i].y = pointList[i].y;
				newList[i].cluster = pointList[i].cluster;
			}
			pointList = newList;
		}
		inFile >> pointList[numberOfPoints].x;
		inFile >> pointList[numberOfPoints].y;
		pointList[numberOfPoints].cluster = -1;
		//if (pointList[numberOfPoints].x > maxX)
		//	maxX = pointList[numberOfPoints].x;
		//if (pointList[numberOfPoints].y > maxY)
		//	maxY = pointList[numberOfPoints].y;
		numberOfPoints++;
	}

	//Initialize centroids
	srand(time(NULL));
	for (int c = 0; c < k; c++){
		int randomPoint = rand() % numberOfPoints;
		centroidList[c].x = pointList[randomPoint].x;
		centroidList[c].y = pointList[randomPoint].y;
	}

	//Loop until the centroids do not shift
	do {
		done = true;
		//assign points to centroids
		for (int p = 0; p < numberOfPoints; p++){
			float thisDist = 0;
			float lastDist = FLT_MAX;
			for (int c = 0; c < k; c++){
				thisDist = euDis(pointList[p], centroidList[c]);
				if (thisDist < lastDist){
					pointList[p].cluster = c;
					lastDist = thisDist;
				}
			}

		}

		//adjust centroids
		float xSum[k];
		float ySum[k];
		float totalPoints[k];
		for (int c = 0; c < k; c++){
			xSum[c] = 0;
			ySum[c] = 0;
			totalPoints[c] = 0;
		}
		for (int p = 0; p < numberOfPoints; p++){
			xSum[pointList[p].cluster] += pointList[p].x;
			ySum[pointList[p].cluster] += pointList[p].y;
			totalPoints[pointList[p].cluster] += 1;
		}
		//cout << endl << "New Centroids: " << endl;
		for (int c = 0; c < k; c++){
			float newX = totalPoints[c] > 0?xSum[c] / totalPoints[c]:0;
			float newY = totalPoints[c] > 0?ySum[c] / totalPoints[c]:0;
			if (newX != centroidList[c].x && newX != 0){
				centroidList[c].x = newX;
				done = false;
			}
			if (newY != centroidList[c].y && newY != 0){
				centroidList[c].y = newY;
				done = false;
			}
			//cout << "\t#" << c << " " << centroidList[c].x << " " << centroidList[c].y << endl;
		}
		//numLoops += 1;
		//cout << "Loop: " << numLoops << endl;
	}while(!done);

	//output the result
	for (int p = 0; p < numberOfPoints; p++){
		cout << pointList[p].x << " " << pointList[p].y << " " << pointList[p].cluster + 1 << endl;
		//cout << pointList[p].x << " " << pointList[p].y << " " << palette[pointList[p].cluster] << endl;
	}

	return 0;
}


float euDis(point p, centroid c){
	float dist = pow((p.x - c.x), 2) + pow((p.y - c.y), 2);
	dist = sqrt(dist);
	//cout << p.x << "," << p.y << " to " << c.x << "," << c.y << " is " << dist << endl;
	return dist;
}
