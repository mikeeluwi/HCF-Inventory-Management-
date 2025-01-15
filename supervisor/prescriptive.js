// Get all the tabs and content elements
const tabs = document.querySelectorAll('.tabs-trigger');
const contents = document.querySelectorAll('.tabs-content');

// Add a click event listener to each tab
tabs.forEach((tab, index) => {
  tab.addEventListener('click', () => {
    // Hide all the content
    contents.forEach(content => {
      content.style.display = 'none';
    });

    // Show the content for the selected tab
    contents[index].style.display = 'block';
  });
});
// Get the button and the chart container
const productButton = document.querySelector('.tabs-trigger[value="products"]');
const productChartContainer = document.querySelector('.tabs-content[value="products"]');

// Add a click event listener to the button
productButton.addEventListener('click', () => {
  // Show the chart container
  productChartContainer.style.display = 'block';
});