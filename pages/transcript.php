<?php
/* Note: No credit is provided for submitting design and/or code that is     */
/*       taken from course-provided examples.                                */
/*                                                                           */
/* Do not copy this code into your project submission and then change it.    */
/*                                                                           */
/* Write your own code from scratch. Use this example as a REFERENCE only.   */
/*                                                                           */
/* You may not copy this code, change a few names/variables, and then claim  */
/* it as your own.                                                           */
/*                                                                           */
/* Examples are provided to help you learn. Copying the example and then     */
/* changing it a bit, does not help you learn the learning objectives of     */
/* this assignment. You need to write your own code from scratch to help you */
/* learn.                                                                    */

$page_title = "Transcript";

$nav_transcript_class = "active_page";

include_once("includes/transcript-values.php");

// --- Insert Form Data ---

// Did the user submit the insert form?
if (isset($_POST["request-insert"])) {

  $form_values["class_num"] = ($_POST["course"] == "" ? NULL : (int)$_POST["course"]); // untrusted
  $form_values["term"]      = ($_POST["term"]   == "" ? NULL : (int)$_POST["term"]); // untrusted
  $form_values["year"]      = ($_POST["year"]   == "" ? NULL : (int)$_POST["year"]); // untrusted
  $form_values["grade"]     = ($_POST["grade"]  == "" ? NULL : $_POST["grade"]); // untrusted

  $result = exec_sql_query(
    $db,
    "INSERT INTO grades (course_id, term, acad_year, grade) VALUES (:course, :term, :acad_year, :grade);",
    array(
      ":course"    => $form_values["class_num"], // tainted/untrusted
      ":term"      => $form_values["term"], // tainted/untrusted
      ":acad_year" => $form_values["year"], // tainted/untrusted
      ":grade"     => $form_values["grade"] // tainted/untrusted
    )
  );
}

// --- Select Query Data ---

// CSS classes for sort arrows
$sort_css_classes = array(
  "course_asc" => "inactive",
  "course_desc" => "inactive",
  "term_asc" => "inactive",
  "term_desc" => "inactive",
  "year_asc" => "inactive",
  "year_desc" => "inactive",
  "credits_asc" => "inactive",
  "credits_desc" => "inactive",
  "grade_asc" => "inactive",
  "grade_desc" => "inactive",
);

// URL query string for NEXT sort
$sort_next_url = array(
  "course" => "course",
  "term" => "term",
  "year" => "year",
  "credits" => "credits",
  "grade" => "grade"
);

// URL query string for NEXT order
$order_next_url = array(
  "course" => "asc",
  "term" => "asc",
  "year" => "asc",
  "credits" => "asc",
  "grade" => "asc"
);

// retrieve query string parameters for sorting
$sort_param = $_GET["sort"] ?? NULL; // untrusted
$order_param = $_GET["order"] ?? NULL; // untrusted

// SQL query parts
$sql_select_clause = "TODO: 1. select clause";
$sql_order_clause = "TODO: 2. default order clause"; // no default order

// validate sort's order parameter
// order must be: asc or desc
if ($order_param == "asc") {
  // ascending
  $sql_sort_order = "TODO: 5. sort order (asc)";

  $order_next = "desc";
  $filter_icon = "up";
} else if ($order_param == "desc") {
  // descending
  $sql_sort_order = "TODO: 6. sort order (desc)";

  $order_next = NULL;
  $filter_icon = "down";
} else {
  // no order
  $sql_sort_order = NULL;

  $order_next = "asc";
  $filter_icon = NULL;
}

// validate order parameter.
// sort must be "course", "term", "year", "credits", or "grade"
if ($sql_sort_order && in_array($sort_param, array("course", "term", "year", "credits", "grade"))) {

  // rotate URLS through sort asc, sort desc, sort none
  if ($order_next == NULL) {
    $sort_next_url[$sort_param] = NULL;
  }
  $order_next_url[$sort_param] = $order_next;

  // Table sorter icon should match current sort
  if ($filter_icon == "up") {
    $sort_css_classes[$sort_param . "_asc"] = "";
    $sort_css_classes[$sort_param . "_desc"] = "hidden";
  } else if ($filter_icon == "down") {
    $sort_css_classes[$sort_param . "_asc"] = "hidden";
    $sort_css_classes[$sort_param . "_desc"] = "";
  }

  // SQL sort by field
  // map query string values to database fields
  $sql_sort_fields = array(
    "course" => "TODO: 7. field for course number",
    "term" => "grades.term",
    "year" => "grades.acad_year",
    "credits" => "courses.credits",
    "grade" => "grades.grade"
  );
  $sql_sort_field = $sql_sort_fields[$sort_param];

  // order by SQL clause
  $sql_order_clause = "TODO: 8. order by clause (sort by field ($sql_sort_field) in order ($sql_sort_order)";
} else {
  // sort params are invalid
  $sort_param = NULL;
  $order_param = NULL;
}

// build the final query
// glue the select clause to the order clause
$sql_select_query = "TODO: 3. glue select clause to order clause";

// query grades table
// TODO: 4. query database using the assembled/built query ($sql_select_query)
$records = exec_sql_query($db, "SELECT
  grades.id AS 'grades.id',
  courses.number AS 'courses.number',
  courses.credits AS 'courses.credits',
  grades.term AS 'grades.term',
  grades.acad_year AS 'grades.acad_year',
  grades.grade AS 'grades.grade'
FROM grades INNER JOIN courses ON (grades.course_id = courses.id)")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<?php include "includes/meta.php" ?>

<body>
  <?php include "includes/header.php" ?>

  <main class="transcript">
    <h2><?php echo $page_title; ?></h2>

    <table>
      <tr>
        <th class="column-course">
          <a class="sort" href="/transcript?<?php echo http_build_query(array(
                                              "sort" => $sort_next_url["course"],
                                              "order" => $order_next_url["course"]
                                            )) ?>" aria-label="Sort by Course Number">
            Course
            <svg class="icon" version="1.1" viewBox="0 0 2.1391 4.2339" xmlns="http://www.w3.org/2000/svg">
              <g transform="translate(-38.257 -61.073)">
                <path class="sort_desc <?php echo $sort_css_classes["course_desc"]; ?>" d="m40.396 63.455-1.0695 1.8521-1.0695-1.8521z" />
                <path class="sort_asc <?php echo $sort_css_classes["course_asc"]; ?>" d="m40.396 62.925h-2.1391l1.0695-1.8521z" />
              </g>
            </svg>
          </a>
        </th>

        <th class="column-term">
          <a class="sort" href="/transcript?<?php echo http_build_query(array(
                                              "sort" => $sort_next_url["term"],
                                              "order" => $order_next_url["term"]
                                            )) ?>" aria-label="Sort by Term">
            Term
            <svg class="icon" version="1.1" viewBox="0 0 2.1391 4.2339" xmlns="http://www.w3.org/2000/svg">
              <g transform="translate(-38.257 -61.073)">
                <path class="sort_desc <?php echo $sort_css_classes["term_desc"]; ?>" d="m40.396 63.455-1.0695 1.8521-1.0695-1.8521z" />
                <path class="sort_asc <?php echo $sort_css_classes["term_asc"]; ?>" d="m40.396 62.925h-2.1391l1.0695-1.8521z" />
              </g>
            </svg>
          </a>
        </th>

        <th class="column-year">
          <a class="sort" href="/transcript?<?php echo http_build_query(array(
                                              "sort" => $sort_next_url["year"],
                                              "order" => $order_next_url["year"]
                                            )) ?>" aria-label="Sort by Academic Year">
            Year
            <svg class="icon" version="1.1" viewBox="0 0 2.1391 4.2339" xmlns="http://www.w3.org/2000/svg">
              <g transform="translate(-38.257 -61.073)">
                <path class="sort_desc <?php echo $sort_css_classes["year_desc"]; ?>" d="m40.396 63.455-1.0695 1.8521-1.0695-1.8521z" />
                <path class="sort_asc <?php echo $sort_css_classes["year_asc"]; ?>" d="m40.396 62.925h-2.1391l1.0695-1.8521z" />
              </g>
            </svg>
          </a>
        </th>

        <th class="column-credits">
          <a class="sort" href="/transcript?<?php echo http_build_query(array(
                                              "sort" => $sort_next_url["credits"],
                                              "order" => $order_next_url["credits"]
                                            )) ?>" aria-label="Sort by Academic Credits">
            Credits
            <svg class="icon" version="1.1" viewBox="0 0 2.1391 4.2339" xmlns="http://www.w3.org/2000/svg">
              <g transform="translate(-38.257 -61.073)">
                <path class="sort_desc <?php echo $sort_css_classes["credits_desc"]; ?>" d="m40.396 63.455-1.0695 1.8521-1.0695-1.8521z" />
                <path class="sort_asc <?php echo $sort_css_classes["credits_asc"]; ?>" d="m40.396 62.925h-2.1391l1.0695-1.8521z" />
              </g>
            </svg>
          </a>
        </th>

        <th class="column-grade">
          <a class="sort" href="/transcript?<?php echo http_build_query(array(
                                              "sort" => $sort_next_url["grade"],
                                              "order" => $order_next_url["grade"]
                                            )) ?>" aria-label="Sort by Grade">
            Grade
            <svg class="icon" version="1.1" viewBox="0 0 2.1391 4.2339" xmlns="http://www.w3.org/2000/svg">
              <g transform="translate(-38.257 -61.073)">
                <path class="sort_desc <?php echo $sort_css_classes["grade_desc"]; ?>" d="m40.396 63.455-1.0695 1.8521-1.0695-1.8521z" />
                <path class="sort_asc <?php echo $sort_css_classes["grade_asc"]; ?>" d="m40.396 62.925h-2.1391l1.0695-1.8521z" />
              </g>
            </svg>
          </a>
        </th>

        <th class="min">
          Update
        </th>
      </tr>

      <?php
      // write a table row for each record
      foreach ($records as $record) {
        $course = $record["courses.number"];
        $term = TERM_CODINGS[$record["grades.term"]];
        $year = ACADEMIC_YEAR_CODINGS[$record["grades.acad_year"]];
        $grade = $record["grades.grade"] ?? "";
        $credits = $record["courses.credits"];

        // row partial
        include "includes/transcript-record.php";
      } ?>

    </table>

    <section>
      <h2>Add Student Course Record</h2>

      <form class="insert" action="/transcript?<?php echo http_build_query(array(
                                                  "sort" => $sort_param,
                                                  "order" => $order_param
                                                )) ?>" method="post">

        <div class="label-input">
          <label for="insert-course">Course:</label>
          <select id="insert-course" name="course" required>
            <option value="" disabled selected>Select Course</option>

            <?php foreach ($courses as $course) { ?>
              <option value="<?php echo htmlspecialchars($course["id"]); ?>">
                <?php echo htmlspecialchars($course["number"] . ": " . $course["title"]); ?>
              </option>
            <?php } ?>
          </select>
        </div>

        <div class="label-input">
          <label for="insert-term">Term:</label>
          <select id="insert-term" name="term" required>
            <option value="" disabled selected>Select Term</option>

            <?php foreach (TERM_CODINGS as $code => $term) { ?>
              <option value="<?php echo htmlspecialchars($code); ?>">
                <?php echo htmlspecialchars($term); ?>
              </option>
            <?php } ?>
          </select>
        </div>

        <div class="label-input">
          <label for="insert-year">Academic Year:</label>
          <select id="insert-year" name="year" required>
            <option value="" disabled selected>Select Year</option>

            <?php foreach (ACADEMIC_YEAR_CODINGS as $code => $year) { ?>
              <option value="<?php echo htmlspecialchars($code); ?>">
                <?php echo htmlspecialchars($year); ?>
              </option>
            <?php } ?>
          </select>
        </div>

        <div class="label-input">
          <label for="insert-grade">Grade:</label>
          <select id="insert-grade" name="grade">
            <option value="">No Grade</option>

            <?php foreach (GRADES as $grade) { ?>
              <option value="<?php echo htmlspecialchars($grade); ?>">
                <?php echo htmlspecialchars($grade); ?>
              </option>
            <?php } ?>
          </select>
        </div>

        <div class="align-right">
          <button type="submit" name="request-insert">
            Add Course Record
          </button>
        </div>
      </form>
    </section>

  </main>

  <?php include "includes/footer.php" ?>
</body>

</html>
