@extends('layouts.app')

@section('title', 'Organizational Structure')

@section('content')
<div class="container-fluid">
    <div class="row">
        @include('admin.partials.sidebar')
        <main class="col-md-10 py-4">
            <div class="admin-back-btn-wrap mb-3">
                @if(isset($organization))
                    <a href="{{ route('admin.organizations.profile', $organization->id) }}" class="btn btn-secondary rounded-pill px-3">&lt; Back to Organization Profile</a>
                @else
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary rounded-pill px-3">&lt; Back to Dashboard</a>
                @endif
            </div>

            <div class="py-3">
                @if(isset($organization))
                    <h1 class="h4 mb-4">{{ $organization->name }} - Organizational Structure</h1>
                @else
                    <h1 class="h4 mb-4">Organizational Structure (Admin → Staff)</h1>
                @endif

                <!-- Organizational Structure Chart -->
                <div class="card mb-4 wow fadeInUp" style="margin: 0 7rem; padding-top: 2rem; padding-bottom: 2rem;" data-wow-delay="150ms">
                    <div class="card-header" style="background-color: midnightblue; color: white; position: relative;">
                        <h5 class="mb-0">
                            <i class="bi bi-diagram-3"></i> 
                            @if(isset($organization))
                                Organizational Structure (Staff → Assistants)
                            @else
                                Organizational Structure (Admin → Staff)
                            @endif
                        </h5>
                        <button type="button" id="toggleOrgChart" class="btn btn-sm btn-light" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%);">
                            <i class="bi bi-chevron-up" id="toggleIcon"></i>
                        </button>
                    </div>
                    <div class="card-body" id="orgChartBody" style="background-color: white; color: black; padding: 15px;">
                        <div id="orgChartContainer" style="position: relative; width: 100%; height: 700px; border: 1px solid #ddd; border-radius: 5px; background: #F5F5F5; overflow: auto; overflow-x: auto; overflow-y: auto;">
                            @if(isset($organization))
                                <div id="orgChart" style="width: max-content; min-width: 100%; height: max-content; min-height: 100%;"></div>
                            @else
                                <div id="adminOrgChart" style="width: max-content; min-width: 100%; height: max-content; min-height: 100%;"></div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- vis-network.js for Organizational Chart -->
<script type="text/javascript" src="https://unpkg.com/vis-network/standalone/umd/vis-network.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://unpkg.com/vis-network/styles/vis-network.min.css">

<script>
@if(isset($organization))
// Organization Staff to Assistants Organizational Structure
document.addEventListener('DOMContentLoaded', function() {
    var orgData = @json($orgStructure ?? ['nodes' => [], 'edges' => []]);
    
    if (orgData.nodes && orgData.nodes.length > 0) {
        var nodes = new vis.DataSet(orgData.nodes.map(function(node) {
            // Apply group-specific styling
            var nodeStyle = {
                shape: 'box',
                font: {
                    size: 14,
                    face: 'Arial',
                    color: '#ffffff'
                },
                borderWidth: 2,
                shadow: true,
                margin: 10
            };

            // Apply colors based on group
            if (node.group === 'organization') {
                nodeStyle.color = {
                    background: '#4CAF50',
                    border: '#2E7D32',
                    highlight: {
                        background: '#66BB6A',
                        border: '#388E3C'
                    }
                };
                nodeStyle.font.size = 16;
            } else if (node.group === 'staff') {
                nodeStyle.color = {
                    background: '#2196F3',
                    border: '#1565C0',
                    highlight: {
                        background: '#42A5F5',
                        border: '#1976D2'
                    }
                };
                nodeStyle.font.size = 14;
            } else if (node.group === 'assistant') {
                nodeStyle.color = {
                    background: '#FF9800',
                    border: '#E65100',
                    highlight: {
                        background: '#FFB74D',
                        border: '#F57C00'
                    }
                };
                nodeStyle.font.size = 12;
            }

            return Object.assign(node, nodeStyle);
        }));

        var edges = new vis.DataSet(orgData.edges.map(function(edge) {
            return {
                from: edge.from,
                to: edge.to,
                arrows: {
                    to: {
                        enabled: true,
                        scaleFactor: 1.2,
                        type: 'arrow'
                    }
                },
                color: {
                    color: '#848484',
                    highlight: '#2B7CE9'
                },
                smooth: {
                    type: 'cubicBezier',
                    forceDirection: 'vertical',
                    roundness: 0.4
                },
                width: 2
            };
        }));

        var data = {
            nodes: nodes,
            edges: edges
        };

        var options = {
            layout: {
                hierarchical: {
                    direction: 'UD', // Up to Down
                    sortMethod: 'directed',
                    levelSeparation: 120,
                    nodeSpacing: 150,
                    treeSpacing: 200,
                    blockShifting: true,
                    edgeMinimization: true,
                    parentCentralization: true
                }
            },
            physics: {
                enabled: false // Disable physics for hierarchical layout
            },
            interaction: {
                dragNodes: true,
                dragView: true,
                zoomView: true,
                hover: true
            },
            nodes: {
                chosen: {
                    node: function(values, id, selected, hovering) {
                        if (hovering) {
                            values.borderWidth = 4;
                        }
                    }
                }
            }
        };

        var container = document.getElementById('orgChart');
        if (container) {
            var network = new vis.Network(container, data, options);

            // Make responsive
            window.addEventListener('resize', function() {
                network.fit();
            });

            // Fit on load
            network.once('ready', function() {
                network.fit();
            });

            // Add click event to show details
            network.on('click', function(params) {
                if (params.nodes.length > 0) {
                    var nodeId = params.nodes[0];
                    var nodeData = nodes.get(nodeId);
                    if (nodeData && nodeData.title) {
                        console.log('Selected:', nodeData.title);
                    }
                }
            });
        }
    } else {
        // Show message if no data
        var container = document.getElementById('orgChart');
        if (container) {
            container.innerHTML = '<div style="display: flex; align-items: center; justify-content: center; height: 100%; color: #666;"><p>No organizational structure data available for this organization.</p></div>';
        }
    }
});
@else
// Admin to Staff Organizational Structure
document.addEventListener('DOMContentLoaded', function() {
    // Helper function to escape HTML
    function escapeHtml(text) {
        var map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }
    
    var orgData = @json($orgStructure);
    
    if (orgData.nodes && orgData.nodes.length > 0) {
        var nodes = new vis.DataSet(orgData.nodes.map(function(node) {
            // Apply group-specific styling
            var nodeStyle = {
                font: {
                    size: 12,
                    face: 'Arial',
                    color: '#333333',
                    align: 'center'
                },
                borderWidth: 2,
                shadow: true,
                margin: 10,
                labelHighlightBold: true
            };

            // Apply colors and styling based on group
            if (node.group === 'admin') {
                // Admin node - blue-purple with white body
                nodeStyle.shape = 'box';
                nodeStyle.color = {
                    background: '#9C27B0', // Purple header
                    border: '#7B1FA2',
                    highlight: {
                        background: '#BA68C8',
                        border: '#8E24AA'
                    }
                };
                nodeStyle.font.size = 14;
                nodeStyle.font.color = '#ffffff';
                nodeStyle.widthConstraint = { maximum: 220 };
                nodeStyle.heightConstraint = { maximum: 120 };
                nodeStyle.shapeProperties = {
                    borderRadius: 5
                };
            } else if (node.group === 'staff') {
                // Staff nodes - square containers with purple border
                nodeStyle.shape = 'box';
                nodeStyle.color = {
                    background: '#FFFFFF', // White background
                    border: '#9C27B0', // Purple border
                    highlight: {
                        background: '#F3E5F5',
                        border: '#7B1FA2'
                    }
                };
                nodeStyle.font.size = 10;
                nodeStyle.font.color = '#333333';
                nodeStyle.widthConstraint = { maximum: 180 };
                nodeStyle.heightConstraint = { maximum: 160 };
                nodeStyle.shapeProperties = {
                    borderRadius: 5
                };
                
                // Create custom HTML label with circular image container inside square
                var labelParts = node.label.split('\\n');
                var staffName = labelParts[0] || 'Member name';
                var designation = labelParts[1] || 'Role name';
                var dept = node.department || 'Organization/Department name';
                var imageUrl = node.image || null;
                
                // Build custom HTML label with circular image container
                var imageContainer = '';
                if (imageUrl) {
                    // Use img tag with proper error handling
                    imageContainer = '<div style="width: 60px; height: 60px; border-radius: 50%; margin: 0 auto 10px; overflow: hidden; border: 2px solid #9C27B0; background: #E0E0E0; position: relative;">' +
                        '<img src="' + escapeHtml(imageUrl) + '" style="width: 100%; height: 100%; object-fit: cover; display: block;" ' +
                        'onerror="this.onerror=null; this.style.display=\'none\'; this.parentElement.innerHTML=\'<div style=\'width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:#E0E0E0;color:#666;font-size:24px;\'>👤</div>\';">' +
                        '</div>';
                } else {
                    imageContainer = '<div style="width: 60px; height: 60px; border-radius: 50%; margin: 0 auto 10px; overflow: hidden; border: 2px solid #9C27B0; background: #E0E0E0; display: flex; align-items: center; justify-content: center; color: #666; font-size: 24px;">👤</div>';
                }
                
                node.label = '<div style="text-align: center; padding: 10px; width: 180px; box-sizing: border-box; font-family: Arial, sans-serif;">' +
                    imageContainer +
                    '<div style="font-weight: bold; font-size: 11px; margin-bottom: 4px; color: #333; line-height: 1.2; word-wrap: break-word;">' + escapeHtml(staffName) + '</div>' +
                    '<div style="font-size: 10px; color: #666; margin-bottom: 2px; line-height: 1.2; word-wrap: break-word;">' + escapeHtml(designation) + '</div>' +
                    '<div style="font-size: 9px; color: #999; line-height: 1.2; word-wrap: break-word;">' + escapeHtml(dept) + '</div>' +
                    '</div>';
                node.labelType = 'html';
            }

            // Preserve original node properties
            return Object.assign({}, node, nodeStyle);
        }));

        var edges = new vis.DataSet(orgData.edges.map(function(edge) {
            return {
                from: edge.from,
                to: edge.to,
                arrows: {
                    to: {
                        enabled: true,
                        scaleFactor: 1.2,
                        type: 'arrow'
                    }
                },
                color: {
                    color: '#848484',
                    highlight: '#2B7CE9'
                },
                smooth: {
                    type: 'cubicBezier',
                    forceDirection: 'vertical',
                    roundness: 0.4
                },
                width: 2
            };
        }));

        var data = {
            nodes: nodes,
            edges: edges
        };

        var options = {
            layout: {
                hierarchical: {
                    direction: 'LR', // Left to Right (columns)
                    sortMethod: 'directed',
                    levelSeparation: 250, // Horizontal spacing between levels (columns)
                    nodeSpacing: 180, // Vertical spacing between nodes at same level
                    treeSpacing: 200, // Spacing between subtrees
                    blockShifting: true,
                    edgeMinimization: true,
                    parentCentralization: true,
                    shakeTowards: 'leaves'
                }
            },
            physics: {
                enabled: false // Disable physics for hierarchical layout
            },
            interaction: {
                dragNodes: true,
                dragView: true,
                zoomView: true,
                hover: true,
                tooltipDelay: 200
            },
            nodes: {
                chosen: {
                    node: function(values, id, selected, hovering) {
                        if (hovering) {
                            values.borderWidth = 3;
                            values.shadow = true;
                        }
                    }
                },
                margin: 12,
                shapeProperties: {
                    borderRadius: 5
                }
            },
            edges: {
                length: 200,
                color: {
                    color: '#424242',
                    highlight: '#7B1FA2'
                },
                width: 2,
                smooth: false, // Straight lines instead of curves
                arrows: {
                    to: {
                        enabled: true,
                        scaleFactor: 1.2,
                        type: 'arrow'
                    }
                },
                font: {
                    size: 12,
                    align: 'middle'
                }
            }
        };

        var container = document.getElementById('adminOrgChart');
        var network = new vis.Network(container, data, options);

        // Make responsive
        window.addEventListener('resize', function() {
            if (network) {
                network.fit();
            }
        });

        // Don't auto-fit on load - let structure expand for scrollbars
        network.once('ready', function() {
            // Allow the network to expand beyond viewport
            // The container will show scrollbars when needed
        });
        
        // Toggle minimize/maximize functionality
        var toggleBtn = document.getElementById('toggleOrgChart');
        var toggleIcon = document.getElementById('toggleIcon');
        var orgChartBody = document.getElementById('orgChartBody');
        var isMinimized = false;
        
        if (toggleBtn) {
            toggleBtn.addEventListener('click', function() {
                isMinimized = !isMinimized;
                if (isMinimized) {
                    orgChartBody.style.display = 'none';
                    toggleIcon.className = 'bi bi-chevron-down';
                    toggleBtn.title = 'Maximize';
                } else {
                    orgChartBody.style.display = 'block';
                    toggleIcon.className = 'bi bi-chevron-up';
                    toggleBtn.title = 'Minimize';
                }
            });
        }

        // Add click event to show details and organization links
        network.on('click', function(params) {
            if (params.nodes.length > 0) {
                var nodeId = params.nodes[0];
                var nodeData = nodes.get(nodeId);
                if (nodeData) {
                    // Always show modal with full details
                    showStaffDetailsModal(nodeData);
                }
            }
        });
        
        // Function to show staff details modal
        function showStaffDetailsModal(nodeData) {
            var staffName = nodeData.label ? nodeData.label.split('\\n')[0] : 'N/A';
            var designation = nodeData.label && nodeData.label.split('\\n').length > 1 ? 
                             nodeData.label.split('\\n')[1] : 'N/A';
            var title = nodeData.title || '';
            
            var modalHtml = '<div class="modal fade" id="staffDetailsModal" tabindex="-1" role="dialog" aria-labelledby="staffDetailsModalLabel" aria-hidden="true">' +
                '<div class="modal-dialog modal-dialog-centered" role="document">' +
                '<div class="modal-content">' +
                '<div class="modal-header" style="background-color: midnightblue; color: white;">' +
                '<h5 class="modal-title" id="staffDetailsModalLabel">Staff Details</h5>' +
                '<button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;">' +
                '<span aria-hidden="true">&times;</span>' +
                '</button>' +
                '</div>' +
                '<div class="modal-body">' +
                '<p><strong>Name:</strong> ' + staffName + '</p>' +
                '<p><strong>Designation:</strong> ' + designation + '</p>';
            
            if (title) {
                var titleLines = title.split('\\n');
                titleLines.forEach(function(line) {
                    if (line.trim() && line.includes(':')) {
                        modalHtml += '<p><strong>' + line.split(':')[0] + ':</strong> ' + 
                                   line.split(':').slice(1).join(':').trim() + '</p>';
                    }
                });
            }
            
            if (nodeData.organizations && nodeData.organizations.length > 0) {
                modalHtml += '<hr><p><strong>Click on an organization to view its structure:</strong></p>' +
                    '<ul class="list-group">';
                
                nodeData.organizations.forEach(function(org) {
                    var orgStructureUrl = '{{ route("admin.organizational-structure") }}?organization_id=' + org.id;
                    modalHtml += '<li class="list-group-item">' +
                        '<a href="' + orgStructureUrl + '" class="btn btn-link p-0" style="color: midnightblue; text-decoration: none;">' +
                        '<i class="bi bi-building"></i> ' + org.name +
                        '</a>' +
                        '</li>';
                });
                
                modalHtml += '</ul>';
            } else {
                modalHtml += '<p><em>No organizations assigned</em></p>';
            }
            
            modalHtml += '</div>' +
                '<div class="modal-footer">' +
                '<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '</div>';
            
            // Remove existing modal if any
            var existingModal = document.getElementById('staffDetailsModal');
            if (existingModal) {
                existingModal.remove();
            }
            
            // Add modal to body
            document.body.insertAdjacentHTML('beforeend', modalHtml);
            
            // Show modal
            $('#staffDetailsModal').modal('show');
            
            // Remove modal from DOM when closed
            $('#staffDetailsModal').on('hidden.bs.modal', function() {
                $(this).remove();
            });
        }
    } else {
        // Show message if no data
        document.getElementById('adminOrgChart').innerHTML = '<div style="display: flex; align-items: center; justify-content: center; height: 100%; color: #666;"><p>No organizational structure data available.</p></div>';
    }
});
@endif
</script>

<style>
@if(isset($organization))
#orgChart {
    min-height: 400px;
}

@media (max-width: 768px) {
    #orgChart {
        height: 400px !important;
    }
}

@media (max-width: 576px) {
    #orgChart {
        height: 350px !important;
    }
}
@else
#adminOrgChart {
    min-height: 400px;
}

/* Scrollbar styling for organizational structure container */
#orgChartContainer {
    scrollbar-width: thin;
    scrollbar-color: #9C27B0 #F5F5F5;
}

#orgChartContainer::-webkit-scrollbar {
    width: 12px;
    height: 12px;
}

#orgChartContainer::-webkit-scrollbar-track {
    background: #F5F5F5;
    border-radius: 6px;
}

#orgChartContainer::-webkit-scrollbar-thumb {
    background: #9C27B0;
    border-radius: 6px;
    border: 2px solid #F5F5F5;
}

#orgChartContainer::-webkit-scrollbar-thumb:hover {
    background: #7B1FA2;
}

#orgChartContainer::-webkit-scrollbar-corner {
    background: #F5F5F5;
}

/* Toggle button styling */
#toggleOrgChart {
    z-index: 10;
}

#toggleOrgChart:hover {
    background-color: #e0e0e0 !important;
}

@media (max-width: 1400px) {
    .card.mb-4.wow.fadeInUp[style*="margin: 0 7rem"] {
        margin: 0 6rem !important;
    }
}

@media (max-width: 992px) {
    .card.mb-4.wow.fadeInUp[style*="margin: 0 7rem"] {
        margin: 0 4rem !important;
    }
    #orgChartContainer {
        height: 500px !important;
    }
}

@media (max-width: 768px) {
    .card.mb-4.wow.fadeInUp[style*="margin: 0 7rem"] {
        margin: 0 3rem !important;
    }
    #orgChartContainer {
        height: 400px !important;
    }
}

@media (max-width: 576px) {
    .card.mb-4.wow.fadeInUp[style*="margin: 0 7rem"] {
        margin: 0 2.5rem !important;
    }
    #orgChartContainer {
        height: 350px !important;
    }
}
@endif
</style>

@endsection

