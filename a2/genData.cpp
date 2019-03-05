#include <iostream>
#include <fstream>
#include <cmath>
#include <stdio.h>
#include <stdlib.h>


using namespace std;

struct point{
    float x;
    float y;
};

int main (int argc, char** argv){
    if (argc != 4){
        cerr << "command uasage is \"genData num_cluster cluster_radius num_points\"" << endl;
        return 1;
    }

    int numClusters = atoi(argv[1]);
    float clusterRadius = atof(argv[2]);
    int numPoints = atoi(argv[3]);

    ofstream outFile("randomPoints.txt");
    float range = (clusterRadius * static_cast<float>(numClusters)) ;
    point centroidList[numClusters];

    srand(time(NULL));
    for(int i = 0; i < numClusters; i++){
        centroidList[i].x = rand() % (int)range;
        centroidList[i].y = rand() % (int)range;
    }

    for (int i = 0; i < numPoints; i++){
        int theta = rand() % 360;
        int cluster = rand() % numClusters;
        float thisRadius = rand() % (int)clusterRadius;
        point thisPoint;

        thisPoint.x = floor(centroidList[cluster].x + (thisRadius * cos(theta)));
        thisPoint.y = floor(centroidList[cluster].y + (thisRadius * sin(theta)));

        outFile << thisPoint.x << " " << thisPoint.y << endl;
    }

    return 0;
}
