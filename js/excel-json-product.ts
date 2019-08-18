let logField: HTMLDivElement = null,
  dropField: HTMLDivElement = null,
  progressBar: HTMLProgressElement = null,
  downloadJSON: HTMLAnchorElement = null,
  downloadCSV: HTMLAnchorElement = null,
  reader: FileReader = null;

interface Product {
  sku: string;
  parent: string;
  name: string;
  imageCode: string;
  price: number;
  categories: string[];
  height?: number;
  width?: number;
  length?: number;
  minimumOrderQuantity: number;
  attributes: { [key: string]: string[] };
  discountGroups: string[];
  bulkDiscount: { ppu: number; qty: number }[];
}

interface Attribute {
  name: string;
  columnKey: string;
  slug: string;
  position: number;
  visibility: boolean;
  variation: boolean;
  isTaxonomy: boolean;
}

const attributeMeta: Attribute[] = [
  {
    name: "Ausführung",
    columnKey: "Ausführung",
    slug: "model",
    position: 50,
    visibility: true,
    variation: true,
    isTaxonomy: false
  },
  {
    name: "Pfeilrichtung",
    columnKey: "Pfeilrichtung",
    slug: "arrow-dir",
    position: 0,
    visibility: true,
    variation: true,
    isTaxonomy: false
  },
  {
    name: "Grösse",
    columnKey: "Grösse",
    slug: "size",
    position: 1,
    visibility: true,
    variation: true,
    isTaxonomy: true
  },
  {
    name: "Jahr",
    columnKey: "Jahr",
    slug: "year",
    position: 60,
    visibility: true,
    variation: true,
    isTaxonomy: false
  },
  {
    name: "Farbe",
    columnKey: "Farbe",
    slug: "color",
    position: 70,
    visibility: true,
    variation: true,
    isTaxonomy: false
  },
  {
    name: "Format",
    columnKey: "Format",
    slug: "format",
    position: 80,
    visibility: true,
    variation: true,
    isTaxonomy: false
  },
  {
    name: "Leuchtdichte",
    columnKey: "Leuchtdichte_mcd",
    slug: "luminance",
    position: 40,
    visibility: true,
    variation: true,
    isTaxonomy: true
  },
  {
    name: "Material",
    columnKey: "Material",
    slug: "material",
    position: 10,
    visibility: true,
    variation: true,
    isTaxonomy: true
  },
  {
    name: "Norm",
    columnKey: "Norm",
    slug: "norm",
    position: 90,
    visibility: true,
    variation: true,
    isTaxonomy: false
  },
  {
    name: "PSPA Klasse",
    columnKey: "PSPA_Class",
    slug: "pspa-class",
    position: 100,
    visibility: true,
    variation: true,
    isTaxonomy: false
  },
  {
    name: "Ursprungsland",
    columnKey: "Ursprungsland",
    slug: "country",
    position: 120,
    visibility: true,
    variation: true,
    isTaxonomy: true
  },
  {
    name: "Druckeigenschaft(-en)",
    columnKey: "Eigenschaft_Druck",
    slug: "print-property",
    position: 110,
    visibility: true,
    variation: true,
    isTaxonomy: true
  },
  {
    name: "Einheit",
    columnKey: "Einheit",
    slug: "unit",
    position: 1000,
    visibility: true,
    variation: true,
    isTaxonomy: true
  },
  {
    name: "Symbolnummer",
    columnKey: "Symbolnummer",
    slug: "symbol-number",
    position: 990,
    visibility: true,
    variation: true,
    isTaxonomy: true
  },
  {
    name: "Inhalt",
    columnKey: "Inhalt",
    slug: "content",
    position: 1,
    visibility: true,
    variation: true,
    isTaxonomy: false
  },
  {
    name: "Variante",
    columnKey: "Variante",
    slug: "product_variation",
    position: 0,
    visibility: true,
    variation: true,
    isTaxonomy: false
  }
].sort((a, b) => a.position - b.position);

const attributeKeys = attributeMeta.map(attribute => attribute.columnKey);

const log = (...args) => {
  if (logField) {
    logField.innerHTML += "<br>" + args.join("    ");
  } else {
    console.log(...args);
  }
};

const updateProgress = (value: number) => {
  if (progressBar) {
    progressBar.value = value;
  } else {
    console.log("Progress is now at", value);
  }
};

const excelToJSON = workbook => {
  const sheetNameList = workbook.SheetNames;
  const products: Product[] = [];

  sheetNameList.forEach(sheetName => {
    /* iterate through sheets */
    const XLSX = window["XLSX"];
    const excelProducts = XLSX.utils.sheet_to_json(workbook.Sheets[sheetName]);

    const attributesPerVariation: { [key: string]: string[] } = {},
      discountsPerVariation: { [key: string]: string[] } = {},
      variationParentBySku: { [key: string]: Product } = {};
    excelProducts
      .filter(
        (productData: { [key: string]: any }) =>
          productData["Shop_Produkt_Ja_Nein"] == 1
      )
      .forEach((productData: { [key: string]: any }, index: number) => {
        //Merging variants with each other based on variation code
        try {
          updateProgress(Math.max(index - 1, 0) / excelProducts.length);

          const product: Product = {
            sku: null,
            parent: null,
            name: null,
            price: null,
            categories: [],
            imageCode: null,
            minimumOrderQuantity: null,
            attributes: {},
            discountGroups: [],
            bulkDiscount: []
          };

          if (
            "Artikelname_neu" in productData &&
            productData["Artikelname_neu"].trim().length > 0
          ) {
            product.name = productData["Artikelname_neu"].trim();
          } else {
            throw new Error(
              `The product on line ${index} is ignored as doesn't have the field "Artikelname_neu"`
            );
          }

          if ("Artikel_Nummer_Produkt" in productData) {
            product.sku = productData["Artikel_Nummer_Produkt"];
          } else {
            throw new Error(
              `The product on line ${index} is ignored as it doesn't have a sku!`
            );
          }

          if ("Artikel_Bilder_Code" in productData) {
            product.imageCode = productData["Artikel_Bilder_Code"];
          } else {
            throw new Error(
              `The product on line ${index} is ignored as it doesn't have an image!`
            );
          }

          //Merge BOGEN + Stückzahl pro Einheit
          if (
            "Stückzahl pro Einheit" in productData &&
            productData["Stückzahl pro Einheit"] &&
            "Einheit" in productData
          ) {
            productData["Einheit"] = `${productData["Einheit"]} (${
              productData["Stückzahl pro Einheit"]
            } STK)`;
          }

          if ("Breite" in productData) {
            product.width = parseFloat(productData["Breite"]);
          }

          if ("Höhe" in productData) {
            product.height = parseFloat(productData["Höhe"]);
          }

          if ("Thema" in productData) {
            if (product.categories.indexOf(productData["Thema"]) === -1) {
              product.categories.push(productData["Thema"]);
            }
          }

          if ("Mindestbestellmenge" in productData) {
            product.minimumOrderQuantity = parseInt(
              productData["Mindestbestellmenge"],
              10
            );
          }

          if ("Einzelpreis" in productData) {
            if (
              typeof productData["Einzelpreis"] === "string" &&
              productData["Einzelpreis"].includes("CHF")
            ) {
              product.price = parseFloat(
                productData["Einzelpreis"].trim().split(" ")[1]
              );
            } else if (typeof productData["Einzelpreis"] === "number") {
              product.price = productData["Einzelpreis"];
            } else {
              throw new Error(
                `Ignoring product on line ${index}, productData["Einzelpreis"]: "${
                  productData["Einzelpreis"]
                }" is invalid`
              );
            }

            if (product.price <= 0) {
              throw new Error(
                `The product on line ${index} is ignored as doesn't have a valid field "Einzelpreis"`
              );
            }
          } else {
            throw new Error(
              `The product on line ${index} is ignored as doesn't have the field "Einzelpreis"`
            );
          }

          //Check for discount keys
          for (let column in productData) {
            if (column.indexOf("_Rabattberechtigt") !== -1) {
              const discountGroup = column.replace("_Rabattberechtigt", "");

              product.discountGroups.push(discountGroup);
            }
          }

          if ("Produktgruppe_Shop" in productData) {
            if (!(productData["Produktgruppe_Shop"] in discountsPerVariation)) {
              //add discount groups
              discountsPerVariation[productData["Produktgruppe_Shop"]] = [
                ...product.discountGroups
              ];
            } else {
              //verify discount groups
              discountsPerVariation[productData["Produktgruppe_Shop"]].forEach(
                discountGroup => {
                  if (!product.discountGroups.includes(discountGroup)) {
                    throw new Error(
                      `The product on line ${index} is ignored as it misses the discount group "${discountGroup}"! (Compared with the first product in the group "${
                        product.parent
                      })"`
                    );
                  }
                }
              );

              product.discountGroups.forEach(discountGroup => {
                if (
                  !discountsPerVariation[
                    productData["Produktgruppe_Shop"]
                  ].includes(discountGroup)
                ) {
                  throw new Error(
                    `The product on line ${index} is ignored as it has the additional discount group "${discountGroup}"! (Compared with the first product in the group "${
                      product.parent
                    })"`
                  );
                }
              });
            }
          }

          //Bulk discount

          for (let column in productData) {
            if (column.indexOf("VP Staffel ") !== -1) {
              const pricePerUnit = parseFloat(
                productData[column]
                  .toString()
                  .replace("CHF", "")
                  .trim()
              );
              const quantity = parseInt(
                column.replace("VP Staffel ", "").trim(),
                10
              );

              if (pricePerUnit > 0 && quantity > 0) {
                product.bulkDiscount.push({
                  ppu: pricePerUnit,
                  qty: quantity
                });
              }
            }
          }

          if ("Produktgruppe_Shop" in productData) {
            product.parent = productData["Produktgruppe_Shop"].trim();

            if (!(product.parent in attributesPerVariation)) {
              //add attributes
              attributesPerVariation[product.parent] = [];

              attributeKeys
                .filter(attributeKey => attributeKey in productData)
                .forEach(attributeKey => {
                  attributesPerVariation[product.parent].push(attributeKey);
                  product.attributes[attributeKey] = [
                    productData[attributeKey]
                  ];
                });

              //create parent product of type variable
              variationParentBySku[product.parent] = {
                sku: product.parent,
                name: null,
                parent: null,
                imageCode: null,
                price: null,
                minimumOrderQuantity: 0,
                attributes: JSON.parse(JSON.stringify(product.attributes)), //dirty deep clone
                categories: product.categories,
                discountGroups: [],
                bulkDiscount: []
              };

              products.push(variationParentBySku[product.parent]);
            } else {
              //verify
              const attributes = attributeKeys.filter(
                attributeKey => attributeKey in productData
              );

              attributesPerVariation[product.parent].forEach(attribute => {
                if (!attributes.includes(attribute)) {
                  throw new Error(
                    `The product on line ${index} is ignored as it misses the attribute "${attribute}"! (Compared with the first product in the group "${
                      product.parent
                    })"`
                  );
                }

                product.attributes[attribute] = [productData[attribute]];
              });
              attributes.forEach(attribute => {
                if (
                  !attributesPerVariation[product.parent].includes(attribute)
                ) {
                  throw new Error(
                    `The product on line ${index} is ignored as it has the aditional attribute "${attribute}"! (Compared with the first product in the group "${
                      product.parent
                    })"`
                  );
                }

                variationParentBySku[product.parent].attributes[attribute].push(
                  ...product.attributes[attribute].filter(
                    value =>
                      !variationParentBySku[product.parent].attributes[
                        attribute
                      ].includes(value)
                  )
                );
              });
            }
          }

          products.push(product);
        } catch (e) {
          return log("Error", e.message);
        }
      });

    updateProgress(1);
  });

  return products;
};

const productsToCSV = (products: Product[]) => {
  const maxNumberOfAttributes = products.reduce(
    (maxNumber, product) =>
      Math.max(maxNumber, Object.keys(product.attributes).length),
    0
  );

  const attributeHeader = [];
  for (let i = 0; i < maxNumberOfAttributes; i++) {
    attributeHeader.push(`Attribute ${i} Name`);
    attributeHeader.push(`Attribute ${i} Global`);
    attributeHeader.push(`Attribute ${i} Value(s)`);
  }

  let csv =
    [
      "Type",
      "SKU",
      "Parent",
      "Name",
      "Images",
      "Regular Price",
      "Length",
      "Width",
      "Height",
      "Categories",
      "Discount Groups",
      "Meta: _feuerschutz_variable_bulk_discount_enabled",
      "Meta: _feuerschutz_bulk_discount",
      "Meta: _feuerschutz_min_order_quantity",
      ...attributeHeader
    ].join(",") + "\n";

  return (
    csv +
    products
      .map(product => {
        const attributes = [];

        for (let attributeKey in product.attributes) {
          const meta = attributeMeta.find(
            meta => meta.columnKey === attributeKey
          );
          attributes.push(
            meta.name,
            meta.isTaxonomy ? "1" : "0",
            product.attributes[attributeKey].join(", ")
          );
        }

        const type =
          product.parent !== null
            ? "variation"
            : Object.keys(product.attributes).length > 0
            ? "variable"
            : "simple";

        return (
          '"' +
          [
            type,
            product.sku,
            product.parent,
            product.name,
            product.imageCode,
            product.price,
            product.length,
            product.width,
            product.height,
            product.categories.join(","),
            product.discountGroups.join(","),
            type === "variation" && product.bulkDiscount.length > 0 ? "1" : "0",
            product.bulkDiscount.length > 0
              ? JSON.stringify(product.bulkDiscount)
              : "",
            product.minimumOrderQuantity,
            ...attributes
          ] //remove null fields
            .map((column: string | undefined | number) =>
              column ? column.toString() : ""
            )
            .map((column: string) => column.replace(/"/g, '""'))
            .join('","') +
          '"'
        );
      })
      .join("\n")
  );
};

const readExcel = (e: ProgressEvent) => {
  const data = reader.result;

  const XLSX = window["XLSX"];

  /* if binary string, read with type 'binary' */
  const workbook = XLSX.read(data, { type: "binary" });
  const products = excelToJSON(workbook);

  const jsonDataString =
    "data:text/json;charset=utf-8," +
    encodeURIComponent(JSON.stringify(products));

  downloadJSON.setAttribute("href", jsonDataString);
  downloadJSON.setAttribute("download", "products.json");
  downloadJSON.style.display = "block";

  const csv = productsToCSV(products);

  downloadCSV.setAttribute(
    "href",
    "data:text/csv;charset=utf-8," + encodeURIComponent(csv)
  );
  downloadCSV.setAttribute("download", "products.csv");
  downloadCSV.style.display = "block";
};

window.addEventListener("load", () => {
  //page has fully loaded

  dropField = document.querySelector("div#drop-excel");
  logField = document.querySelector("div#log-filename");
  downloadJSON = document.querySelector("a#download-json");
  downloadCSV = document.querySelector("a#download-csv");
  progressBar = document.querySelector("progress#progress");

  dropField.addEventListener("dragover", e => {
    e.preventDefault();
    e.stopPropagation();

    dropField.classList.add("dragging");
  });

  dropField.addEventListener("dragleave", e => {
    e.preventDefault();
    e.stopPropagation();

    dropField.classList.remove("dragging");
  });

  dropField.addEventListener("drop", e => {
    e.stopPropagation();
    e.preventDefault();

    const files = e.dataTransfer.files;
    if (files.length > 1) {
      return alert("Only one file at a time!");
    }

    reader = new FileReader();
    reader.onload = readExcel;

    log("Reading and parsing " + files[0].name + " ...");

    reader.readAsBinaryString(files[0]);
  });
});
