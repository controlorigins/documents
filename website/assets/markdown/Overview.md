# Document Repository

Welcome to the  Documentation Repository! This repository is dedicated to storing and managing documentation for our company's various domains.

- [Published Website](https://controlorigins-docs.azurewebsites.net/?page=homepage)
- [GitHub Repository](https://github.com/controlorigins/documents)

## Table of Contents

- [Introduction](#introduction)
- [Folder Structure](#folder-structure)
- [Contributing](#contributing)
- [License](#license)

## Introduction

This repository is designed to house documentation in Markdown (.md) format for different domains within our company. Markdown is a lightweight and easy-to-read markup language that all contributors should use when creating or editing documentation files. Markdown allows us to maintain consistent formatting and structure across all our documents.

## Folder Structure

We use a folder structure to organize documentation by domains or categories. Each domain will have its dedicated folder with relevant markdown files. Here is an example of the folder structure:
Please ensure that you place your documentation files in the appropriate folders to maintain organization.

~~~
assets\markdown\Overview.md (Root of Document Folder, this file)
assets\markdown\ChatGPT (ChatGPT domain folder)
assets\markdown\ChatGPT\1_ChatGPT Overview.md (ChatGPT Overview Document)
~~~


## Contributing

We encourage all employees to contribute to the documentation repository. To contribute, follow these steps:

```mermaid
graph TD;
  A[Start] --> B[Create Branch];
  B --> C[Make Changes];
  C --> D[Commit Changes];
  D --> E[Push Branch];
  E --> F[Create Pull Request];
  F --> |Review & Approve| G[Approved?];
  G --> |No| H[Rejected];
  H --> I[Revise Changes];
  I --> D; 
  G --> |Yes| J[Merge Changes];
  J --> K[End];
```

1. After creating a pull request (F), it goes through a review and approval process (G).
1. If the pull request is not approved (No), it is marked as "Rejected" (H).
1. The author revises the changes (I) and then commits and pushes the branch (D and E) to create a new pull request (F) for review.
1. If the pull request is approved (Yes), the changes are merged (J) into the main document.
1. Finally, the workflow ends (K) after the changes are merged.

This diagram visualizes the workflow for both approved and rejected pull requests, as well as the process of revising changes when a pull request is rejected.
By contributing to this repository, you agree that all files you create or modify remain the property of Control Origins.

## License

All documentation in this repository is proprietary and confidential to Control Origins.  
Unauthorized distribution or sharing of the documentation is strictly prohibited.  
