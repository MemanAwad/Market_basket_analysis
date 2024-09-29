# Apriori Algorithm Implementation

## Overview

This project implements the Apriori algorithm to discover frequent itemsets and generate association rules from transaction data stored in a CSV file. The algorithm identifies items that frequently co-occur in transactions, allowing businesses to understand purchasing behavior and optimize their inventory or marketing strategies.

## Features

- Load transaction data from a CSV file.
- Clean and preprocess the data to remove inconsistencies.
- Generate frequent itemsets based on a minimum support threshold.
- Generate association rules based on a minimum confidence threshold.
- Output the supported item sets and generated association rules.

## Requirements

- PHP 7.0 or higher
- Composer (for dependency management, if required)
- A web server to run PHP files

## Installation

1. **Clone the repository:**
   ```bash
   git clone https://github.com/yourusername/apriori-algorithm.git
   cd apriori-algorithm
